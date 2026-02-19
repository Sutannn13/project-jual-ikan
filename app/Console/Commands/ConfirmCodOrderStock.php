<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmCodOrderStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:confirm-cod-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Confirm stock for COD orders that are older than 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find COD orders that are:
        // 1. Payment method is 'cod'
        // 2. Status is 'confirmed' or 'out_for_delivery'
        // 3. Created more than 1 hour ago
        // 4. Stock not yet confirmed (stock_confirmed_at is null)
        $codOrders = Order::where('payment_method', 'cod')
            ->whereIn('status', ['confirmed', 'out_for_delivery'])
            ->whereNull('stock_confirmed_at')
            ->where('created_at', '<=', now()->subHour())
            ->with(['items.produk' => function ($q) {
                $q->withTrashed();
            }])
            ->get();

        $count = 0;
        $errors = [];

        foreach ($codOrders as $order) {
            try {
                DB::transaction(function () use ($order) {
                    // Lock and confirm stock for each item
                    foreach ($order->items as $item) {
                        if ($item->produk) {
                            $produk = Produk::withTrashed()->lockForUpdate()->find($item->produk_id);
                            if ($produk) {
                                // Confirm stock: move from reserved to actual deduction
                                $produk->confirmStock($item->qty);
                            }
                        }
                    }

                    // Mark stock as confirmed
                    $order->update(['stock_confirmed_at' => now()]);
                });

                $count++;
                Log::info("COD Order {$order->order_number} stock confirmed automatically.");
                $this->info("✓ Order {$order->order_number} stock confirmed");
            } catch (\Exception $e) {
                $errors[] = "Order {$order->order_number}: {$e->getMessage()}";
                Log::error("Failed to confirm COD order stock", [
                    'order' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Order {$order->order_number}: {$e->getMessage()}");
            }
        }

        if ($count > 0) {
            $this->info("Successfully confirmed stock for {$count} COD order(s).");
        } else {
            $this->info("No COD orders found that need stock confirmation.");
        }

        if (count($errors) > 0) {
            $this->warn("Encountered " . count($errors) . " error(s) during processing.");
        }

        return Command::SUCCESS;
    }
}
