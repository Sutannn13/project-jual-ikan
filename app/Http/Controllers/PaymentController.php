<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create Midtrans Snap Token for an order
     */
    public function createSnapToken(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Order tidak bisa dibayar.'], 400);
        }

        // Prevent creating snap token if already uploaded manual payment
        if ($order->payment_proof) {
            return response()->json(['error' => 'Anda sudah upload bukti transfer manual. Silakan tunggu verifikasi admin.'], 400);
        }

        // If snap token already exists and order is still pending, reuse it
        if ($order->midtrans_snap_token) {
            return response()->json([
                'snap_token' => $order->midtrans_snap_token,
            ]);
        }

        // Use grand total (already includes shipping)
        $grandTotal = (int) $order->total_price;

        $payload = [
            'transaction_details' => [
                'order_id'     => $order->order_number . '-' . time(),
                'gross_amount' => $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->user->no_hp ?? '',
            ],
            'item_details' => [[
                'id'       => 'ORDER-' . $order->order_number,
                'price'    => $grandTotal,
                'quantity' => 1,
                'name'     => 'Pesanan ' . $order->order_number,
            ]],
            'callbacks' => [
                'finish' => route('order.success', $order),
            ],
        ];

        try {
            $serverKey = config('midtrans.server_key');
            $snapUrl = config('midtrans.snap_url');

            $response = Http::withBasicAuth($serverKey, '')
                ->post($snapUrl, $payload);

            if ($response->successful()) {
                $snapToken = $response->json('token');

                $order->update([
                    'midtrans_snap_token' => $snapToken,
                ]);

                return response()->json([
                    'snap_token' => $snapToken,
                ]);
            }

            Log::error('Midtrans Snap Error', [
                'order' => $order->order_number,
                'response' => $response->json(),
            ]);

            return response()->json([
                'error' => 'Gagal membuat transaksi pembayaran.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Midtrans Exception', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat menghubungi payment gateway.',
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $payload = $request->all();

        // Verify signature
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans invalid signature', $payload);
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Extract original order number (remove timestamp suffix)
        $parts = explode('-', $orderId);
        if (count($parts) >= 4) {
            // FM-2026-0001-timestamp
            $orderNumber = implode('-', array_slice($parts, 0, 3));
        } else {
            $orderNumber = $orderId;
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::warning('Midtrans order not found', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? '';

        Log::info('Midtrans Notification', [
            'order' => $orderNumber,
            'status' => $transactionStatus,
            'type' => $paymentType,
            'current_order_status' => $order->status,
        ]);

        // PREVENT DOUBLE PAYMENT: Ignore if order already paid via manual transfer
        if ($order->payment_method === 'manual_transfer') {
            Log::warning('Midtrans notification ignored: Order already paid via manual transfer', [
                'order' => $orderNumber,
                'status' => $order->status,
            ]);
            return response()->json(['message' => 'Order already processed via manual transfer'], 200);
        }

        // PREVENT DOUBLE PAYMENT: Ignore if order already in paid/confirmed/completed status
        if (in_array($order->status, ['paid', 'confirmed', 'out_for_delivery', 'completed'])) {
            Log::warning('Midtrans notification ignored: Order already processed', [
                'order' => $orderNumber,
                'current_status' => $order->status,
            ]);
            return response()->json(['message' => 'Order already processed'], 200);
        }

        // Update Midtrans transaction data
        $order->update([
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            'payment_method' => $paymentType,
        ]);

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                if (in_array($order->status, ['pending', 'waiting_payment'])) {
                    $oldStatus = $order->status;
                    
                    // Confirm stock deduction (move from reserved to actual)
                    foreach ($order->items as $item) {
                        $item->produk->confirmStock($item->qty);
                    }
                    
                    $order->update([
                        'status' => 'paid',
                        'payment_uploaded_at' => now(),
                    ]);

                    // Send email notification
                    $this->sendStatusEmail($order, $oldStatus, 'paid');
                }
                break;

            case 'pending':
                // Keep status as pending, waiting for actual payment
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                if (in_array($order->status, ['pending', 'waiting_payment'])) {
                    $oldStatus = $order->status;
                    
                    // Release reserved stock
                    foreach ($order->items as $item) {
                        $item->produk->releaseStock($item->qty);
                    }
                    
                    $order->update(['status' => 'cancelled']);
                    $this->sendStatusEmail($order, $oldStatus, 'cancelled');
                }
                break;
        }

        return response()->json(['status' => 'OK']);
    }

    private function sendStatusEmail(Order $order, string $oldStatus, string $newStatus): void
    {
        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->send(new \App\Mail\OrderStatusMail($order, $oldStatus, $newStatus));
        } catch (\Exception $e) {
            Log::error('Failed to send order status email', ['error' => $e->getMessage()]);
        }
    }
}
