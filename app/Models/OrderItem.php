<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'produk_id', 'price_per_kg', 'qty', 'subtotal', 'cost_price',
    ];

    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'qty' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }
}
