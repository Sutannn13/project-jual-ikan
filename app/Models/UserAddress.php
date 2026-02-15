<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'penerima', 'telepon', 'alamat_lengkap',
        'kecamatan', 'kota', 'provinsi', 'kode_pos', 'catatan', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set this address as default and unset others
     */
    public function setAsDefault(): void
    {
        // Unset all other defaults for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Get full address string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat_lengkap,
            $this->kecamatan,
            $this->kota,
            $this->provinsi,
            $this->kode_pos,
        ]);
        return implode(', ', $parts);
    }
}
