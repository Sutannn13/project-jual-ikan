<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'user_role', 'action', 'model',
        'model_id', 'description', 'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // ─── Relations ─────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ────────────────────────────
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ─── Helpers ───────────────────────────

    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description,
        ?string $model = null,
        ?int $modelId = null,
        ?array $changes = null
    ): self {
        $user = auth()->user();

        return self::create([
            'user_id'    => $user?->id,
            'user_name'  => $user?->name ?? 'System',
            'user_role'  => $user?->role ?? 'system',
            'action'     => $action,
            'model'      => $model,
            'model_id'   => $modelId,
            'description'=> $description,
            'changes'    => $changes,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created'    => 'fas fa-plus-circle text-green-400',
            'updated'    => 'fas fa-edit text-blue-400',
            'deleted'    => 'fas fa-trash text-red-400',
            'verified'   => 'fas fa-check-circle text-emerald-400',
            'rejected'   => 'fas fa-times-circle text-red-400',
            'cancelled'  => 'fas fa-ban text-orange-400',
            'login'      => 'fas fa-sign-in-alt text-cyan-400',
            'logout'     => 'fas fa-sign-out-alt text-gray-400',
            'status_changed' => 'fas fa-exchange-alt text-purple-400',
            'uploaded'   => 'fas fa-upload text-teal-400',
            'confirmed'  => 'fas fa-thumbs-up text-green-400',
            default      => 'fas fa-circle text-white/40',
        };
    }

    /**
     * Get action color for badge
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created'    => 'green',
            'updated'    => 'blue',
            'deleted'    => 'red',
            'verified'   => 'emerald',
            'rejected'   => 'red',
            'cancelled'  => 'orange',
            'login'      => 'cyan',
            'status_changed' => 'purple',
            'uploaded'   => 'teal',
            'confirmed'  => 'green',
            default      => 'gray',
        };
    }
}
