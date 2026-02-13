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
        $expiredOrders = Order::expiredPending()->with('items.produk')->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            // Release reserved stock (don't restore to physical stock)
            foreach ($order->items as $item) {
                if ($item->produk) {
                    $item->produk->releaseStock($item->qty);
                }
            }

            // Update status to cancelled
            $order->update([
                'status' => 'cancelled',
                'rejection_reason' => 'Dibatalkan otomatis: Batas waktu pembayaran telah terlewati.',
            ]);

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
