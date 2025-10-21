<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'tipo',
        'valor',
        'data_pagamento',
        'forma_pagamento',
        'payment_channel',
        'payment_channel_id',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'valor' => 'decimal:2'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function channel()
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }
}
