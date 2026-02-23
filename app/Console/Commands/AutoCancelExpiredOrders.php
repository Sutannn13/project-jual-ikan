<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel orders that have exceeded payment deadline (auto-cancel after 24 hours)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Order::expiredPending()->with(['items.produk' => function ($q) {
            $q->withTrashed(); // Include soft-deleted products for stock release
        }])->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                // Lock and release reserved stock
                foreach ($order->items as $item) {
                    if ($item->produk) {
                        $produk = \App\Models\Produk::withTrashed()->lockForUpdate()->find($item->produk_id);
                        if ($produk) {
                            $produk->releaseStock($item->qty);
                        }
                    }
                }

                // Update status to cancelled
                $order->update([
                    'status' => 'cancelled',
                    'rejection_reason' => 'Dibatalkan otomatis: Batas waktu pembayaran telah terlewati.',
                ]);

                // Log the status change
                $order->logStatusChange('cancelled', $order->getOriginal('status'), 'Auto-cancelled: batas waktu pembayaran terlewati (24 jam).');

                // Notify customer via cart and admin notification
                try {
                    \App\Services\AdminNotificationService::orderCancelled($order, 'Auto-cancel: expired payment deadline');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to send auto-cancel notification for order {$order->order_number}: " . $e->getMessage());
                }
            });

            $count++;
            Log::info("Order {$order->order_number} auto-cancelled due to expired payment deadline.");
        }

        if ($count > 0) {
            $this->info("Successfully cancelled {$count} expired order(s).");
        } else {
            $this->info("No expired orders found.");
        }

        return Command::SUCCESS;
    }
}
