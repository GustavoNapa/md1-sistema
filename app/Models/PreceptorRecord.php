<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreceptorRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'nome_preceptor',
        'crm',
        'especialidade',
        'hospital',
        'observacoes'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
