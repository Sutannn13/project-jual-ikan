<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\ActivityLog;
use App\Services\AdminNotificationService;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    /**
     * Customer mengajukan refund.
     *
     * Uses lockForUpdate() to prevent duplicate refund requests
     * from concurrent form submissions (double-click, etc.)
     */
    public function request(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $request->validate([
            'refund_reason' => 'required|string|max:1000',
        ], [
            'refund_reason.required' => 'Alasan refund wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                // Lock order row to prevent concurrent refund requests
                $order = Order::lockForUpdate()->findOrFail($order->id);

                if (!$order->canRequestRefund()) {
                    throw new \RuntimeException('Pesanan ini tidak bisa diajukan refund.');
                }

                $order->update([
                    'refund_status' => 'requested',
                    'refund_reason' => $request->refund_reason,
                    'refund_requested_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Notifications OUTSIDE transaction
        AdminNotification::create([
            'type'         => 'refund_request',
            'priority'     => 'urgent',
            'title'        => 'Permintaan Refund!',
            'message'      => "Pesanan #{$order->order_number} mengajukan refund: " . \Illuminate\Support\Str::limit($request->refund_reason, 60),
            'icon'         => 'fas fa-undo',
            'color'        => 'red',
            'related_id'   => $order->id,
            'related_type' => Order::class,
            'action_url'   => route('admin.orders.show', $order),
            'action_text'  => 'Proses Refund',
        ]);

        ActivityLog::log('refund_requested', "Refund diajukan untuk pesanan #{$order->order_number} oleh {$order->user->name}", 'Order', $order->id);

        return back()->with('success', 'Permintaan refund berhasil diajukan. Admin akan segera memprosesnya.');
    }

    /**
     * Customer melihat status refund
     */
    public function status(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        return response()->json([
            'refund_status' => $order->refund_status,
            'refund_reason' => $order->refund_reason,
            'refund_admin_note' => $order->refund_admin_note,
            'refund_requested_at' => $order->refund_requested_at?->format('d M Y H:i'),
            'refund_processed_at' => $order->refund_processed_at?->format('d M Y H:i'),
        ]);
    }

    /**
     * Admin: Daftar permintaan refund
     */
    public function adminIndex()
    {
        $refunds = Order::where('refund_status', '!=', 'none')
            ->with(['user', 'items.produk'])
            ->latest('refund_requested_at')
            ->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Admin: Approve refund.
     *
     * CRITICAL: Double-check refund_status inside lock to prevent double approval.
     */
    public function approve(Request $request, Order $order)
    {
        try {
            DB::transaction(function () use ($order, $request) {
                $order = Order::lockForUpdate()->findOrFail($order->id);

                // Double-check inside lock — another admin may have already processed
                if ($order->refund_status !== 'requested') {
                    throw new \RuntimeException('Refund ini sudah diproses.');
                }

                // Restore stock
                foreach ($order->items as $item) {
                    $produk = Produk::lockForUpdate()->find($item->produk_id);
                    if ($produk) {
                        // Kembalikan stok fisik (karena sudah di-confirmStock sebelumnya)
                        $produk->increment('stok', $item->qty);
                    }
                }

                $order->update([
                    'refund_status' => 'approved',
                    'refund_admin_note' => $request->input('admin_note', 'Refund disetujui.'),
                    'refund_processed_at' => now(),
                    'status' => 'cancelled',
                ]);

                $order->logStatusChange('cancelled', $order->getOriginal('status'), 'Refund disetujui, pesanan dibatalkan', auth()->id());
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        ActivityLog::log('refund_approved', "Refund pesanan #{$order->order_number} disetujui. Stok dikembalikan.", 'Order', $order->id);

        // Email notification to customer
        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->send(new \App\Mail\OrderStatusMail($order, 'paid', 'cancelled'));
        } catch (\Exception $e) {
            Log::error('Failed to send refund email', ['error' => $e->getMessage()]);
        }

        return back()->with('success', "Refund pesanan #{$order->order_number} berhasil disetujui. Stok dikembalikan.");
    }

    /**
     * Admin: Reject refund.
     *
     * Uses lock to prevent race condition with concurrent approve/reject.
     */
    public function reject(Request $request, Order $order)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                $order = Order::lockForUpdate()->findOrFail($order->id);

                if ($order->refund_status !== 'requested') {
                    throw new \RuntimeException('Refund ini sudah diproses.');
                }

                $order->update([
                    'refund_status' => 'rejected',
                    'refund_admin_note' => $request->admin_note,
                    'refund_processed_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        ActivityLog::log('refund_rejected', "Refund pesanan #{$order->order_number} ditolak: {$request->admin_note}", 'Order', $order->id);

        return back()->with('success', "Refund pesanan #{$order->order_number} ditolak.");
    }
}
