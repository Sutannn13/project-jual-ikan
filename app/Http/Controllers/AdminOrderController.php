<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderStatusMail;
use Illuminate\Http\Request;
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

        // Count per status untuk filter
        $statusCounts = [
            'pending' => Order::where('status', 'pending')->count(),
            'waiting_payment' => Order::where('status', 'waiting_payment')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'out_for_delivery' => Order::where('status', 'out_for_delivery')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.produk', 'statusHistories.updatedByUser']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Admin verifikasi pembayaran (Terima)
     */
    public function verifyPayment(Order $order)
    {
        if ($order->status !== 'waiting_payment') {
            return back()->with('error', 'Order ini tidak dalam status menunggu verifikasi.');
        }

        if (!$order->payment_proof) {
            return back()->with('error', 'Bukti pembayaran belum diupload oleh customer.');
        }

        // Confirm stock deduction (move from reserved to actual)
        foreach ($order->items as $item) {
            $item->produk->confirmStock($item->qty);
        }

        $order->update([
            'status' => 'paid',
            'rejection_reason' => null,
        ]);

        $order->logStatusChange('paid', 'waiting_payment', 'Pembayaran diverifikasi oleh admin', auth()->id());

        AdminNotificationService::paymentVerified($order);

        return back()->with('success', "Pembayaran order {$order->order_number} berhasil diverifikasi!");
    }

    /**
     * Admin tolak pembayaran dengan alasan
     */
    public function rejectPayment(Request $request, Order $order)
    {
        if ($order->status !== 'waiting_payment') {
            return back()->with('error', 'Order ini tidak dalam status menunggu verifikasi.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        // Hapus bukti bayar
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Reset ke pending agar customer bisa upload ulang
        // Stock tetap reserved
        $order->update([
            'payment_proof' => null,
            'payment_uploaded_at' => null,
            'status' => 'pending',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $order->logStatusChange('pending', 'waiting_payment', 'Pembayaran ditolak: ' . $request->rejection_reason, auth()->id());

        AdminNotificationService::paymentRejected($order, $request->rejection_reason);

        $this->sendStatusEmail($order, 'waiting_payment', 'pending');

        return back()->with('success', "Pembayaran order {$order->order_number} ditolak. Customer akan melihat alasan penolakan.");
    }

    /**
     * Admin konfirmasi pesanan dengan info kurir (setelah pembayaran terverifikasi)
     */
    public function confirm(Request $request, Order $order)
    {
        // Hanya bisa konfirmasi jika status = paid
        if ($order->status !== 'paid') {
            return back()->with('error', 'Pembayaran belum diverifikasi. Verifikasi dulu sebelum konfirmasi pesanan.');
        }

        $request->validate([
            'delivery_note'   => 'required|string|max:500',
            'delivery_time'   => 'nullable|date',
            'courier_name'    => 'nullable|string|max:100',
            'courier_phone'   => 'nullable|string|max:20',
            'tracking_number' => 'nullable|string|max:50',
        ]);

        $order->update([
            'status'          => 'confirmed',
            'delivery_note'   => $request->delivery_note,
            'delivery_time'   => $request->delivery_time,
            'courier_name'    => $request->courier_name,
            'courier_phone'   => $request->courier_phone,
            'tracking_number' => $request->tracking_number,
        ]);

        $order->logStatusChange('confirmed', 'paid', 'Pesanan dikonfirmasi. Kurir: ' . ($request->courier_name ?? '-'), auth()->id());

        AdminNotificationService::orderConfirmed($order);

        $this->sendStatusEmail($order, 'paid', 'confirmed');

        return back()->with('success', "Order {$order->order_number} berhasil dikonfirmasi!");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,waiting_payment,paid,confirmed,out_for_delivery,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If cancelling, release reserved stock
        if ($newStatus === 'cancelled' && !in_array($oldStatus, ['cancelled'])) {
            foreach ($order->items as $item) {
                // If already paid, need to restore actual stock + release reserve
                if (in_array($oldStatus, ['paid', 'confirmed', 'out_for_delivery', 'completed'])) {
                    $item->produk->increment('stok', $item->qty);
                } else {
                    // Just release reserved
                    $item->produk->releaseStock($item->qty);
                }
            }
        }
        // If un-cancelling (re-activating)
        elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                // Reserve stock again
                if (!$item->produk->reserveStock($item->qty)) {
                    return back()->with('error', "Stok {$item->produk->nama} tidak mencukupi untuk mengaktifkan kembali order ini.");
                }
            }
        }
        // If changing to paid from waiting_payment (manual status change)
        elseif ($newStatus === 'paid' && $oldStatus === 'waiting_payment') {
            // Validate stock is still available before confirming
            foreach ($order->items as $item) {
                if ($item->produk->reserved_stock < $item->qty) {
                    return back()->with('error', "Stock {$item->produk->nama} tidak cukup (reserved: {$item->produk->reserved_stock}, needed: {$item->qty}). Order tidak bisa dikonfirmasi.");
                }
            }
            
            foreach ($order->items as $item) {
                $item->produk->confirmStock($item->qty);
            }
        }

        $order->update(['status' => $newStatus]);

        $order->logStatusChange($newStatus, $oldStatus, 'Status diubah manual oleh admin', auth()->id());

        AdminNotificationService::orderStatusChanged($order, $oldStatus, $newStatus);

        $this->sendStatusEmail($order, $oldStatus, $newStatus);

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
     * Bulk actions for orders
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

        switch ($action) {
            case 'mark_processing':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'paid')->get();
                foreach ($orders as $order) {
                    $order->update(['status' => 'confirmed']);
                    $order->logStatusChange('confirmed', 'paid', 'Dikonfirmasi via bulk action', auth()->id());
                    $this->sendStatusEmail($order, 'paid', 'confirmed');
                    $count++;
                }
                $message = "{$count} pesanan berhasil dikonfirmasi.";
                break;

            case 'mark_shipped':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'confirmed')->get();
                foreach ($orders as $order) {
                    $order->update(['status' => 'out_for_delivery']);
                    $order->logStatusChange('out_for_delivery', 'confirmed', 'Dikirim via bulk action', auth()->id());
                    $this->sendStatusEmail($order, 'confirmed', 'out_for_delivery');
                    $count++;
                }
                $message = "{$count} pesanan berhasil diubah ke Dalam Pengiriman.";
                break;

            case 'mark_completed':
                $orders = Order::whereIn('id', $orderIds)->where('status', 'out_for_delivery')->get();
                foreach ($orders as $order) {
                    $order->update(['status' => 'completed']);
                    $order->logStatusChange('completed', 'out_for_delivery', 'Selesai via bulk action', auth()->id());
                    $this->sendStatusEmail($order, 'out_for_delivery', 'completed');
                    $count++;
                }
                $message = "{$count} pesanan berhasil diselesaikan.";
                break;

            case 'mark_cancelled':
                $orders = Order::whereIn('id', $orderIds)
                    ->whereIn('status', ['pending', 'waiting_payment'])->get();
                foreach ($orders as $order) {
                    foreach ($order->items as $item) {
                        $item->produk->releaseStock($item->qty);
                    }
                    $order->update(['status' => 'cancelled']);
                    $order->logStatusChange('cancelled', $order->status, 'Dibatalkan via bulk action', auth()->id());
                    $this->sendStatusEmail($order, $order->status, 'cancelled');
                    $count++;
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

        return back()->with('success', $message);
    }
}
