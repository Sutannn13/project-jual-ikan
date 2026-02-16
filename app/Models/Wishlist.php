<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id', 'produk_id', 'notify_when_available', 'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_when_available' => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
