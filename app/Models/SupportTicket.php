<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'user_id', 'order_id', 'subject', 'category', 'priority', 'status', 'assigned_to', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(TicketMessage::class, 'ticket_id')->latest('created_at');
    }

    /**
     * Generate unique ticket number: TK-2026-XXXX
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = static::whereYear('created_at', $year)->orderByDesc('id')->first();
        $nextNumber = $lastTicket ? (intval(substr($lastTicket->ticket_number, -4)) + 1) : 1;
        return 'TK-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'order_issue'     => 'Masalah Pesanan',
            'payment'         => 'Masalah Pembayaran',
            'product_quality' => 'Kualitas Produk',
            'delivery'        => 'Pengiriman',
            'other'           => 'Lainnya',
            default           => $this->category,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'             => 'Terbuka',
            'in_progress'      => 'Sedang Diproses',
            'waiting_customer' => 'Menunggu Balasan',
            'resolved'         => 'Selesai',
            'closed'           => 'Ditutup',
            default            => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'             => 'red',
            'in_progress'      => 'orange',
            'waiting_customer' => 'yellow',
            'resolved'         => 'green',
            'closed'           => 'gray',
            default            => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'red',
            'medium' => 'yellow',
            'low'    => 'green',
            default  => 'gray',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'Tinggi',
            'medium' => 'Sedang',
            'low'    => 'Rendah',
            default  => $this->priority,
        };
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }
}
