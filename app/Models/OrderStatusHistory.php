<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'status', 'old_status', 'notes', 'updated_by', 'location', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get status label in Bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'Pesanan Dibuat',
            'waiting_payment'  => 'Bukti Bayar Diupload',
            'paid'             => 'Pembayaran Diverifikasi',
            'confirmed'        => 'Pesanan Dikonfirmasi',
            'out_for_delivery' => 'Dalam Pengiriman',
            'completed'        => 'Pesanan Selesai',
            'cancelled'        => 'Pesanan Dibatalkan',
            default            => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Get icon for status
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'fa-clock',
            'waiting_payment'  => 'fa-upload',
            'paid'             => 'fa-check-circle',
            'confirmed'        => 'fa-clipboard-check',
            'out_for_delivery' => 'fa-truck',
            'completed'        => 'fa-flag-checkered',
            'cancelled'        => 'fa-times-circle',
            default            => 'fa-info-circle',
        };
    }

    /**
     * Get color for status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'yellow',
            'waiting_payment'  => 'orange',
            'paid'             => 'cyan',
            'confirmed'        => 'blue',
            'out_for_delivery' => 'indigo',
            'completed'        => 'green',
            'cancelled'        => 'red',
            default            => 'gray',
        };
    }
}
