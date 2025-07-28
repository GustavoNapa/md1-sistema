<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faturamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'mes_ano',
        'valor',
        'data_vencimento',
        'status',
        'observacoes',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}


