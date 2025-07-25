<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'valor',
        'metodo_pagamento',
        'data_pagamento',
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
}
