<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Mail\OrderStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\AdminNotificationService;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.produk'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        // Count per status untuk filter — single query instead of 7 separate ones
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are present (default to 0)
        $allStatuses = ['pending', 'waiting_payment', 'paid', 'confirmed', 'out_for_delivery', 'completed', 'cancelled'];
        foreach ($allStatuses as $s) {
            $statusCounts[$s] = $statusCounts[$s] ?? 0;
        }

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.produk', 'statusHistories.updatedByUser']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin verifikasi pembayaran (Terima).
     *
     * CRITICAL: Wrapped in DB::transaction with lockForUpdate() to prevent:
     * - Two admins verifying the same payment simultaneously
     * - Stock being deducted twice (confirmStock race condition)
     */
    public function verifyPayment(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Re-fetch with pessimistic lock inside transaction
                $order = Order::lockForUpdate()->findOrFail($order->id);

                // Double-check status inside lock — another admin may have verified first
                if ($order->status !== 'waiting_payment') {
                    throw new \RuntimeException('Order ini tidak dalam status menunggu verifikasi.');
                }

                if (!$order->payment_proof) {
                    throw new \RuntimeException('Bukti pembayaran belum diupload oleh customer.');
                }

                // Confirm stock deduction with per-product locks to prevent overselling
                foreach ($order->items as $item) {
                    $produk = Produk::lockForUpdate()->find($item->produk_id);
                    if ($produk) {
                        $produk->confirmStock($item->qty);
                    }
                }

                $order->update([
                    'status' => 'paid',
                    'rejection_reason' => null,
                ]);

                $order->logStatusChange('paid', 'waiting_payment', 'Pembayaran diverifikasi oleh admin', auth()->id());
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Side-effects OUTSIDE transaction (non-critical, shouldn't block commit)
        AdminNotificationService::paymentVerified($order->fresh());
        $this->sendStatusEmail($order->fresh(), 'waiting_payment', 'paid');

        return back()->with('success', "Pembayaran order {$order->order_number} berhasil diverifikasi!");
    }

    /**
     * Admin tolak pembayaran dengan alasan.
     *
     * Uses transaction to ensure atomic status reset + file cleanup.
     */
    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $paymentProofPath = null;

            DB::transaction(function () use ($order, $request, &$paymentProofPath) {
                $order = Order::lockForUpdate()->findOrFail($order->id);

                if ($order->status !== 'waiting_payment') {
                    throw new \RuntimeException('Order ini tidak dalam status menunggu verifikasi.');
                }

                // Remember old file path for deletion AFTER commit
                $paymentProofPath = $order->payment_proof;

                // Reset ke pending agar customer bisa upload ulang. Stock tetap reserved.
                $order->update([
                    'payment_proof' => null,
                    'payment_uploaded_at' => null,
                    'status' => 'pending',
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $order->logStatusChange('pending', 'waiting_payment', 'Pembayaran ditolak: ' . $request->rejection_reason, auth()->id());
            });

            // Delete old file AFTER successful commit (prevent orphan files on rollback)
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminNotificationService::paymentRejected($order->fresh(), $request->rejection_reason);
        $this->sendStatusEmail($order->fresh(), 'waiting_payment', 'pending');

        return back()->with('success', "Pembayaran order {$order->order_number} ditolak. Customer akan melihat alasan penolakan.");
    }

    /**
     * Admin konfirmasi pesanan dengan info kurir (setelah pembayaran terverifikasi)
     */
    public function confirm(Request $request, Order $order)
    {
        $request->validate([
            'delivery_note'   => 'required|string|max:500',
            'delivery_time'   => 'nullable|date',
            'courier_name'    => 'nullable|string|max:100',
            'courier_phone'   => 'nullable|string|max:20',
            'tracking_number' => 'nullable|string|max:50',
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                $order = Order::lockForUpdate()->findOrFail($order->id);

                if ($order->status !== 'paid') {
                    throw new \RuntimeException('Pembayaran belum diverifikasi. Verifikasi dulu sebelum konfirmasi pesanan.');
                }

                $order->update([
                    'status'          => 'confirmed',
                    'delivery_note'   => $request->delivery_note,
                    'delivery_time'   => $request->delivery_time,
                    'courier_name'    => $request->courier_name,
                    'courier_phone'   => $request->courier_phone,
                    'tracking_number' => $request->tracking_number,
                ]);

                $order->logStatusChange('confirmed', 'paid', 'Pesanan dikonfirmasi. Kurir: ' . ($request->courier_name ?? '-'), auth()->id());
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        AdminNotificationService::orderConfirmed($order->fresh());
        $this->sendStatusEmail($order->fresh(), 'paid', 'confirmed');

        return back()->with('success', "Order {$order->order_number} berhasil dikonfirmasi!");
    }

    /**
     * Admin manually update order status.
     *
     * CRITICAL: All stock mutations wrapped in DB::transaction with lockForUpdate()
     * to prevent race conditions between concurrent admin actions.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,waiting_payment,paid,confirmed,out_for_delivery,completed,cancelled',
        ]);

        $newStatus = $request->status;

        try {
            DB::transaction(function () use ($order, $newStatus) {
                // Lock the order row to prevent concurrent status changes
                $order = Order::lockForUpdate()->findOrFail($order->id);
                $oldStatus = $order->status;

                // Prevent no-op
                if ($oldStatus === $newStatus) {
                    throw new \RuntimeException('Status sudah sama, tidak ada perubahan.');
                }

                // === CANCELLING: release stock ===
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $produk = Produk::lockForUpdate()->find($item->produk_id);
                        if (!$produk) continue;

                        if (in_array($oldStatus, ['paid', 'confirmed', 'out_for_delivery', 'completed'])) {
                            // Stock already deducted — restore it
                            $produk->increment('stok', $item->qty);
                        } else {
                            // Stock only reserved — release reservation
                            $produk->releaseStock($item->qty);
                        }
                    }
                }
                // === UN-CANCELLING: re-reserve stock ===
                elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $produk = Produk::lockForUpdate()->find($item->produk_id);
                        if (!$produk || !$produk->reserveStock($item->qty)) {
                            throw new \RuntimeException("Stok {$produk?->nama} tidak mencukupi untuk mengaktifkan kembali order ini.");
                        }
                    }
                }
                // === CONFIRMING PAYMENT (manual): deduct from reserved stock ===
                elseif ($newStatus === 'paid' && $oldStatus === 'waiting_payment') {
                    foreach ($order->items as $item) {
                        $produk = Produk::lockForUpdate()->find($item->produk_id);
                        if (!$produk || ($produk->reserved_stock ?? 0) < $item->qty) {
                            throw new \RuntimeException("Stock {$produk?->nama} tidak cukup (reserved: {$produk?->reserved_stock}, needed: {$item->qty}).");
                        }
                        $produk->confirmStock($item->qty);
                    }
                }

                $order->update(['status' => $newStatus]);
                $order->logStatusChange($newStatus, $oldStatus, 'Status diubah manual oleh admin', auth()->id());
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $order->refresh();
        AdminNotificationService::orderStatusChanged($order, $request->status, $request->status);
        $this->sendStatusEmail($order, $order->status, $request->status);

        return back()->with('success', "Status order {$order->order_number} berhasil diubah!");
    }

    /**
     * Send email notification for status change
     */
    private function sendStatusEmail(Order $order, string $oldStatus, string $newStatus): void
    {
        try {
            Mail::to($order->user->email)
                ->send(new OrderStatusMail($order, $oldStatus, $newStatus));
        } catch (\Exception $e) {
            Log::error('Failed to send order status email', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bulk actions for orders.
     *
     * Each order is processed in its own transaction to prevent partial failures
     * from corrupting stock across multiple orders.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            'bulk_action' => 'required|in:mark_processing,mark_shipped,mark_completed,mark_cancelled,send_reminder',
        ]);

        $orderIds = $request->input('order_ids');
        $action = $request->input('bulk_action');
        $count = 0;
        $errors = [];

        switch ($action) {
            case 'mark_processing':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'paid')->get();
                foreach ($orders as $order) {
                    try {
                        DB::transaction(function () use ($order) {
                            $order = Order::lockForUpdate()->findOrFail($order->id);
                            if ($order->status !== 'paid') return;
                            $order->update(['status' => 'confirmed']);
                            $order->logStatusChange('confirmed', 'paid', 'Dikonfirmasi via bulk action', auth()->id());
                        });
                        $this->sendStatusEmail($order->fresh(), 'paid', 'confirmed');
                        $count++;
                    } catch (\Exception $e) {
                        $errors[] = $order->order_number;
                    }
                }
                $message = "{$count} pesanan berhasil dikonfirmasi.";
                break;

            case 'mark_shipped':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'confirmed')->get();
                foreach ($orders as $order) {
                    try {
                        DB::transaction(function () use ($order) {
                            $order = Order::lockForUpdate()->findOrFail($order->id);
                            if ($order->status !== 'confirmed') return;
                            $order->update(['status' => 'out_for_delivery']);
                            $order->logStatusChange('out_for_delivery', 'confirmed', 'Dikirim via bulk action', auth()->id());
                        });
                        $this->sendStatusEmail($order->fresh(), 'confirmed', 'out_for_delivery');
                        $count++;
                    } catch (\Exception $e) {
                        $errors[] = $order->order_number;
                    }
                }
                $message = "{$count} pesanan berhasil diubah ke Dalam Pengiriman.";
                break;

            case 'mark_completed':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'out_for_delivery')->get();
                foreach ($orders as $order) {
                    try {
                        DB::transaction(function () use ($order) {
                            $order = Order::lockForUpdate()->findOrFail($order->id);
                            if ($order->status !== 'out_for_delivery') return;
                            $order->update(['status' => 'completed']);
                            $order->logStatusChange('completed', 'out_for_delivery', 'Selesai via bulk action', auth()->id());
                        });
                        $this->sendStatusEmail($order->fresh(), 'out_for_delivery', 'completed');
                        $count++;
                    } catch (\Exception $e) {
                        $errors[] = $order->order_number;
                    }
                }
                $message = "{$count} pesanan berhasil diselesaikan.";
                break;

            case 'mark_cancelled':
                $orders = Order::whereIn('id', $orderIds)
                    ->whereIn('status', ['pending', 'waiting_payment'])->get();
                foreach ($orders as $order) {
                    try {
                        DB::transaction(function () use ($order) {
                            $order = Order::lockForUpdate()->findOrFail($order->id);
                            if (!in_array($order->status, ['pending', 'waiting_payment'])) return;

                            $oldStatus = $order->status;
                            foreach ($order->items as $item) {
                                $produk = Produk::lockForUpdate()->find($item->produk_id);
                                if ($produk) {
                                    $produk->releaseStock($item->qty);
                                }
                            }
                            $order->update(['status' => 'cancelled']);
                            $order->logStatusChange('cancelled', $oldStatus, 'Dibatalkan via bulk action', auth()->id());
                        });
                        $this->sendStatusEmail($order->fresh(), $order->status, 'cancelled');
                        $count++;
                    } catch (\Exception $e) {
                        $errors[] = $order->order_number;
                    }
                }
                $message = "{$count} pesanan berhasil dibatalkan.";
                break;

            case 'send_reminder':
                $orders = Order::whereIn('id', $orderIds)
                    ->where('status', 'pending')
                    ->with('user')->get();
                foreach ($orders as $order) {
                    try {
                        Mail::to($order->user->email)->send(new OrderStatusMail($order, 'pending', 'pending'));
                        $count++;
                    } catch (\Exception $e) {
                        Log::error('Failed to send reminder', ['order' => $order->order_number]);
                    }
                }
                $message = "{$count} email reminder berhasil terkirim.";
                break;

            default:
                $message = 'Aksi tidak dikenali.';
        }

        if (!empty($errors)) {
            $message .= ' Gagal: ' . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }
}
