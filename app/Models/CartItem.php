<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'produk_id', 'qty'];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }

    /**
     * Subtotal for this cart item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->produk ? (float) $this->produk->harga_per_kg * (float) $this->qty : 0;
    }
}
