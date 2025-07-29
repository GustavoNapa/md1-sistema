<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'webhook_url',
        'event_type',
        'payload',
        'attempt_number',
        'response_status',
        'response_body',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}

