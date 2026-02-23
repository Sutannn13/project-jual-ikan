<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappTemplate extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'pesan', 'kategori', 'is_active', 'created_by'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function blastLogs(): HasMany
    {
        return $this->hasMany(WhatsappBlastLog::class, 'template_id');
    }

    /**
     * Replace placeholders in the message.
     * Available variables: {nama}, {produk}, {harga}, {stok}, {tanggal}, {toko}
     */
    public function renderMessage(array $variables = []): string
    {
        $defaults = [
            '{toko}'    => config('app.name', 'FishMarket'),
            '{tanggal}' => now()->format('d/m/Y'),
        ];

        $all = array_merge($defaults, $variables);

        return str_replace(array_keys($all), array_values($all), $this->pesan);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
