<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renovacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'data_inicio',
        'data_fim',
        'valor',
        'status',
        'observacoes',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}


