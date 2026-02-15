<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    protected $fillable = [
        'produk_id', 'user_id', 'qty', 'stok_sebelum', 'stok_sesudah',
        'harga_modal', 'supplier', 'catatan',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
        'harga_modal' => 'decimal:2',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
