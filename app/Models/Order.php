<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Order extends Model
{
    /**
     * Mass-assignable fields.
     * NOTE: 'status' and 'refund_status' are intentionally EXCLUDED from $fillable
     * to prevent accidental mass-assignment. Use explicit $order->update(['status' => ...]) 
     * only from trusted controller code.
     */
    protected $fillable = [
        'user_id', 'order_number', 'total_price', 'shipping_cost', 'shipping_zone_id', 'status',
        'payment_proof', 'payment_uploaded_at', 'payment_deadline',
        'rejection_reason',
        'delivery_note', 'delivery_time',
        'courier_name', 'courier_phone', 'tracking_number',
        'courier_driver_id',
        'midtrans_snap_token', 'midtrans_transaction_id', 'payment_method',
        'refund_status', 'refund_reason', 'refund_admin_note', 'refund_requested_at', 'refund_processed_at',
        'stock_reserved_at', 'stock_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'delivery_time' => 'datetime',
            'payment_uploaded_at' => 'datetime',
            'payment_deadline' => 'datetime',
            'refund_requested_at' => 'datetime',
            'refund_processed_at' => 'datetime',
            'stock_reserved_at' => 'datetime',
            'stock_confirmed_at' => 'datetime',
        ];
    }

    public function courierDriver()
    {
        return $this->belongsTo(CourierDriver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Log status change to history
     */
    public function logStatusChange(string $newStatus, ?string $oldStatus = null, ?string $notes = null, ?int $updatedBy = null, ?string $location = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $this->id,
            'status' => $newStatus,
            'old_status' => $oldStatus,
            'notes' => $notes,
            'updated_by' => $updatedBy,
            'location' => $location,
            'created_at' => now(),
        ]);
    }

    /**
     * Generate unique order number: FM-2026-XXXX
     *
     * CRITICAL: Uses lockForUpdate() to prevent duplicate order numbers
     * when two users checkout simultaneously. Must be called inside DB::transaction.
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = static::whereYear('created_at', $year)
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();
        $nextNumber = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
        return 'FM-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'Menunggu Pembayaran',
            'waiting_payment'  => 'Menunggu Verifikasi',
            'paid'             => 'Pembayaran Dikonfirmasi',
            'confirmed'        => 'Pesanan Dikonfirmasi',
            'out_for_delivery' => 'Dalam Pengiriman',
            'completed'        => 'Selesai',
            'cancelled'        => 'Dibatalkan',
            default            => $this->status,
        };
    }

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

    public function getProgressPercentAttribute(): int
    {
        return match ($this->status) {
            'pending'          => 10,
            'waiting_payment'  => 25,
            'paid'             => 40,
            'confirmed'        => 55,
            'out_for_delivery' => 75,
            'completed'        => 100,
            'cancelled'        => 0,
            default            => 0,
        };
    }

    /**
     * Check if user can cancel this order
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment']);
    }

    /**
     * Check if user can request refund
     * Refund hanya bisa diajukan setelah pembayaran diterima tapi SEBELUM pengiriman
     */
    public function canRequestRefund(): bool
    {
        return in_array($this->status, ['paid', 'confirmed'])
            && $this->refund_status === 'none';
    }

    /**
     * Check if payment proof can be uploaded
     */
    public function canUploadPayment(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment']);
    }

    /**
     * Check if payment deadline has passed
     */
    public function isPaymentExpired(): bool
    {
        if (!$this->payment_deadline) {
            return false;
        }
        return Carbon::now()->greaterThan($this->payment_deadline);
    }

    /**
     * Get remaining time for payment in human readable format
     */
    public function getRemainingTimeAttribute(): ?string
    {
        if (!$this->payment_deadline || $this->status !== 'pending') {
            return null;
        }
        
        if ($this->isPaymentExpired()) {
            return 'Waktu habis';
        }
        
        return $this->payment_deadline->diffForHumans(['parts' => 2]);
    }

    /**
     * Get remaining seconds for countdown timer
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->payment_deadline || $this->status !== 'pending') {
            return 0;
        }
        
        $diff = Carbon::now()->diffInSeconds($this->payment_deadline, false);
        return max(0, $diff);
    }

    /**
     * Check if order was rejected (has rejection reason)
     */
    public function wasRejected(): bool
    {
        return !empty($this->rejection_reason);
    }

    /**
     * Scope: Orders that need auto-cancel (pending + waiting_payment + expired)
     */
    public function scopeExpiredPending($query)
    {
        return $query->whereIn('status', ['pending', 'waiting_payment'])
                     ->whereNotNull('payment_deadline')
                     ->where('payment_deadline', '<', Carbon::now());
    }

    /**
     * Calculate gross profit (revenue - cost) for this order
     */
    public function getGrossProfitAttribute(): float
    {
        $profit = 0;
        
        foreach ($this->items as $item) {
            // Use price_per_kg field to avoid rounding errors
            $itemProfit = (($item->price_per_kg ?? ($item->subtotal / $item->qty)) - ($item->cost_price ?? 0)) * $item->qty;
            $profit += $itemProfit;
        }
        
        return round($profit, 2);
    }

    /**
     * Calculate net profit (gross profit - shipping cost if applicable)
     */
    public function getNetProfitAttribute(): float
    {
        // For now, net profit = gross profit
        // Future: subtract operational costs, shipping subsidy, etc.
        return $this->gross_profit;
    }

    /**
     * Get grand total including shipping.
     *
     * NOTE: total_price already includes shipping_cost (set in checkout).
     * This accessor returns total_price as-is. If the storage semantics
     * ever change to store item-only subtotal, update this accordingly.
     */
    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_price;
    }
}
