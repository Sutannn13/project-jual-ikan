<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'type', 'priority', 'title', 'message', 'icon', 'color',
        'related_id', 'related_type', 'action_url', 'action_text',
        'is_read', 'read_at', 'read_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ─── Relations ─────────────────────────
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    // ─── Scopes ────────────────────────────
    public function scopeUnread($query)
    {
        return $query->where('is_read', false)
                     ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                     ->orderBy('created_at', 'desc');
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_read', false)
                     ->whereIn('priority', ['urgent', 'high']);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ───────────────────────────
    public static function unreadCount(): int
    {
        return self::where('is_read', false)->count();
    }

    public static function urgentCount(): int
    {
        return self::where('is_read', false)->whereIn('priority', ['urgent', 'high'])->count();
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => auth()->id(),
        ]);
    }

    public static function markAllAsRead(): void
    {
        self::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => auth()->id(),
        ]);
    }

    /**
     * Auto-resolve icon from type
     */
    public function getResolvedIconAttribute(): string
    {
        if ($this->icon) return $this->icon;

        return match ($this->type) {
            'new_order'         => 'fas fa-shopping-cart',
            'payment_uploaded'  => 'fas fa-money-bill-wave',
            'new_chat'          => 'fas fa-comments',
            'new_review'        => 'fas fa-star',
            'low_stock'         => 'fas fa-exclamation-triangle',
            'order_cancelled'   => 'fas fa-times-circle',
            'order_completed'   => 'fas fa-check-circle',
            'stock_out'         => 'fas fa-box-open',
            default             => 'fas fa-bell',
        };
    }

    /**
     * Auto-resolve color from priority
     */
    public function getResolvedColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high'   => 'orange',
            'medium' => 'cyan',
            'low'    => 'gray',
            default  => 'cyan',
        };
    }
}
