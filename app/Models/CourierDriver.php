<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierDriver extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'no_hp', 'kendaraan', 'zona', 'status', 'catatan'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'Aktif',
            'inactive'    => 'Tidak Aktif',
            'on_delivery' => 'Sedang Antar',
            default       => 'Unknown',
        };
    }

    /**
     * Status badge color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'green',
            'inactive'    => 'gray',
            'on_delivery' => 'blue',
            default       => 'gray',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Current delivery count (orders being delivered)
     */
    public function getActiveDeliveriesCountAttribute(): int
    {
        return $this->orders()->where('status', 'out_for_delivery')->count();
    }
}
