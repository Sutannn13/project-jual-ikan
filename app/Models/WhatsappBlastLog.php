<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappBlastLog extends Model
{
    protected $fillable = ['template_id', 'sent_by', 'pesan_terkirim', 'jumlah_penerima', 'target_phones'];

    protected $casts = [
        'target_phones' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsappTemplate::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
