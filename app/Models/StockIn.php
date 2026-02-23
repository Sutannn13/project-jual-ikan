<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    protected $fillable = [
        'produk_id', 'user_id', 'qty', 'stok_sebelum', 'stok_sesudah',
        'harga_modal', 'supplier', 'catatan', 'expiry_date', 'expiry_notified',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
        'harga_modal' => 'decimal:2',
        'expiry_date' => 'date',
        'expiry_notified' => 'boolean',
    ];

    /**
     * Check if stock is expired or expiring soon (within $days days)
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 3): bool
    {
        return $this->expiry_date
            && !$this->isExpired()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) return 'unknown';
        if ($this->isExpired()) return 'expired';
        if ($this->isExpiringSoon(1)) return 'critical'; // expires today or tomorrow
        if ($this->isExpiringSoon(3)) return 'warning';  // expires in 3 days
        return 'ok';
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
