<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'no_hp', 'alamat', 'foto_profil',
        'email_verified_at', 'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(\App\Models\UserAddress::class)->where('is_default', true);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(\App\Models\Produk::class, 'wishlists');
    }

    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }

    public function salesTargets()
    {
        return $this->hasMany(\App\Models\SalesTarget::class, 'created_by');
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\ChatMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\ChatMessage::class, 'receiver_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user sudah pernah membeli produk tertentu (dengan status completed)
     */
    public function hasPurchased($produkId): bool
    {
        return $this->orders()
            ->where('status', 'completed')
            ->whereHas('items', function ($q) use ($produkId) {
                $q->where('produk_id', $produkId);
            })
            ->exists();
    }

    /**
     * Cek apakah user sudah mereview produk tertentu di order tertentu
     */
    public function hasReviewed($produkId, $orderId): bool
    {
        return $this->reviews()
            ->where('produk_id', $produkId)
            ->where('order_id', $orderId)
            ->exists();
    }

    /**
     * Dapatkan order completed yang memiliki produk tertentu
     */
    public function completedOrdersWithProduct($produkId)
    {
        return $this->orders()
            ->where('status', 'completed')
            ->whereHas('items', function ($q) use ($produkId) {
                $q->where('produk_id', $produkId);
            })
            ->get();
    }
}
