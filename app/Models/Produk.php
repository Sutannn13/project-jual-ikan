<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama', 'kategori', 'harga_per_kg', 'harga_modal', 'stok', 'low_stock_threshold', 'low_stock_notified', 'foto', 'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_kg' => 'decimal:2',
            'stok' => 'decimal:1',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function isLowStock(): bool
    {
        return $this->stok <= $this->low_stock_threshold;
    }

    /**
     * Get available stock (physical stock - reserved)
     */
    public function getAvailableStockAttribute(): float
    {
        return $this->stok - ($this->reserved_stock ?? 0);
    }

    /**
     * Reserve stock for pending order
     */
    public function reserveStock(float $qty): bool
    {
        if ($this->availableStock < $qty) {
            return false;
        }

        $this->increment('reserved_stock', $qty);
        return true;
    }

    /**
     * Release reserved stock (when order cancelled/expired)
     */
    public function releaseStock(float $qty): void
    {
        $this->decrement('reserved_stock', min($qty, $this->reserved_stock ?? 0));
    }

    /**
     * Confirm stock deduction (when payment verified)
     */
    public function confirmStock(float $qty): void
    {
        // Move from reserved to actual deduction
        $this->decrement('reserved_stock', min($qty, $this->reserved_stock ?? 0));
        $this->decrement('stok', $qty);
    }

    /**
     * Cek apakah user tertentu bisa mereview produk ini
     */
    public function canBeReviewedBy($userId): bool
    {
        return \App\Models\Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('items', function ($q) {
                $q->where('produk_id', $this->id);
            })
            ->exists();
    }

    /**
     * Dapatkan order completed yang memiliki produk ini untuk user tertentu
     */
    public function completedOrdersForUser($userId)
    {
        return \App\Models\Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereHas('items', function ($q) {
                $q->where('produk_id', $this->id);
            })
            ->get();
    }
}