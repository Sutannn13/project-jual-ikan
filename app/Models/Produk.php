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
     * Reserve stock for pending order.
     * Must be called inside DB::transaction with lockForUpdate().
     *
     * @throws \RuntimeException if insufficient available stock
     */
    public function reserveStock(float $qty): bool
    {
        if ($qty <= 0) {
            return false;
        }

        if ($this->availableStock < $qty) {
            return false;
        }

        $this->increment('reserved_stock', $qty);
        return true;
    }

    /**
     * Release reserved stock (when order cancelled/expired).
     * Guards against releasing more than what's reserved.
     */
    public function releaseStock(float $qty): void
    {
        if ($qty <= 0) return;

        $releaseAmount = min($qty, $this->reserved_stock ?? 0);
        if ($releaseAmount > 0) {
            $this->decrement('reserved_stock', $releaseAmount);
        }
    }

    /**
     * Confirm stock deduction (when payment verified).
     * Moves from reserved to actual deduction.
     *
     * GUARD: Prevents stock from going negative. If reserved_stock is
     * less than qty (shouldn't happen), we only release what's reserved
     * but still deduct the full qty from physical stock.
     */
    public function confirmStock(float $qty): void
    {
        if ($qty <= 0) return;

        // Release from reserved (cap at actual reserved amount)
        $reserveRelease = min($qty, $this->reserved_stock ?? 0);
        if ($reserveRelease > 0) {
            $this->decrement('reserved_stock', $reserveRelease);
        }

        // Deduct from physical stock (guard against negative)
        $deductAmount = min($qty, $this->stok);
        if ($deductAmount > 0) {
            $this->decrement('stok', $deductAmount);
        }
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