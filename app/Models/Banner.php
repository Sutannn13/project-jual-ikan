<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Banner extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'link_url', 'position',
        'sort_order', 'is_active', 'start_date', 'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Scope: Banner yang aktif dan dalam periode tampil
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', Carbon::today());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', Carbon::today());
            });
    }

    /**
     * Scope: By position
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Check if banner is currently running
     */
    public function isRunning(): bool
    {
        if (!$this->is_active) return false;
        
        $today = Carbon::today();
        if ($this->start_date && $today->lt($this->start_date)) return false;
        if ($this->end_date && $today->gt($this->end_date)) return false;
        
        return true;
    }
}
