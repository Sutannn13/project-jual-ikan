<?php

namespace App\Observers;

use App\Models\Produk;
use App\Services\AdminNotificationService;

class ProdukObserver
{
    /**
     * Handle the Produk "updated" event.
     * Auto-trigger low stock / stock out notifications.
     */
    public function updated(Produk $produk): void
    {
        // Only trigger if stock changed
        if (!$produk->isDirty('stok')) return;

        $threshold = $produk->low_stock_threshold ?? 10;

        if ($produk->stok <= 0) {
            AdminNotificationService::stockOut($produk);
        } elseif ($produk->stok <= $threshold) {
            AdminNotificationService::lowStock($produk);
        }
    }
}
