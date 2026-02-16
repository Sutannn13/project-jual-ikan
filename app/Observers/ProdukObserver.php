<?php

namespace App\Observers;

use App\Models\Produk;
use App\Models\Wishlist;
use App\Mail\ProductBackInStockMail;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProdukObserver
{
    /**
     * Handle the Produk "updated" event.
     * Auto-trigger low stock / stock out notifications + back-in-stock alerts.
     */
    public function updated(Produk $produk): void
    {
        // Only trigger if stock changed
        if (!$produk->isDirty('stok')) return;

        $threshold = $produk->low_stock_threshold ?? 10;
        $oldStock = $produk->getOriginal('stok');

        if ($produk->stok <= 0) {
            AdminNotificationService::stockOut($produk);
        } elseif ($produk->stok <= $threshold) {
            AdminNotificationService::lowStock($produk);
        }

        // Back-in-stock: jika stok berubah dari 0 (atau sangat rendah) ke > 0
        if ($oldStock <= 0 && $produk->stok > 0) {
            $this->notifyWishlistUsers($produk);
        }
    }

    /**
     * Notify wishlist users that product is back in stock
     */
    private function notifyWishlistUsers(Produk $produk): void
    {
        try {
            $wishlisters = Wishlist::where('produk_id', $produk->id)
                ->where('notify_when_available', true)
                ->whereNull('notified_at')
                ->with('user')
                ->get();

            foreach ($wishlisters as $wishlist) {
                try {
                    Mail::to($wishlist->user->email)->send(new ProductBackInStockMail($produk, $wishlist->user));
                    $wishlist->update(['notified_at' => now()]);
                } catch (\Exception $e) {
                    Log::error('Failed to send back-in-stock email', [
                        'produk' => $produk->nama,
                        'user' => $wishlist->user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($wishlisters->count() > 0) {
                AdminNotificationService::create(
                    'restock_notify',
                    "{$wishlisters->count()} pelanggan dinotifikasi: {$produk->nama} kembali tersedia",
                    $produk->id,
                    Produk::class
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to process back-in-stock notifications', ['error' => $e->getMessage()]);
        }
    }
}
