<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SalesTarget extends Model
{
    protected $fillable = ['type', 'target_amount', 'target_date', 'created_by', 'notes'];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'target_date'   => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get actual sales for this target's period
     */
    public function getActualSalesAttribute(): float
    {
        $query = Order::where('status', 'completed');

        if ($this->type === 'daily') {
            $query->whereDate('created_at', $this->target_date);
        } else {
            $query->whereYear('created_at', $this->target_date->year)
                  ->whereMonth('created_at', $this->target_date->month);
        }

        return (float) $query->sum('total_price');
    }

    /**
     * Progress percentage (0-100, capped)
     */
    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->actual_sales / $this->target_amount) * 100, 1));
    }

    /**
     * Whether the target has been achieved
     */
    public function getIsAchievedAttribute(): bool
    {
        return $this->actual_sales >= $this->target_amount;
    }

    /**
     * Get the current active daily target
     */
    public static function todayTarget(): ?self
    {
        return static::where('type', 'daily')
                     ->whereDate('target_date', today())
                     ->first();
    }

    /**
     * Get the current active monthly target
     */
    public static function thisMonthTarget(): ?self
    {
        return static::where('type', 'monthly')
                     ->whereYear('target_date', now()->year)
                     ->whereMonth('target_date', now()->month)
                     ->first();
    }
}
