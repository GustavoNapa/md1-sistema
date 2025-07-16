<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'titulo',
        'data_sessao',
        'duracao',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data_sessao' => 'datetime',
        'duracao' => 'integer'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
