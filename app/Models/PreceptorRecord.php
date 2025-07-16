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
        'historico_preceptor',
        'data_preceptor_informado',
        'data_preceptor_contato',
        'nome_secretaria',
        'email_clinica',
        'whatsapp_clinica',
        'usm',
        'acesso_vitrine_gmc',
        'medico_celebridade'
    ];

    protected $casts = [
        'data_preceptor_informado' => 'date',
        'data_preceptor_contato' => 'date',
        'usm' => 'boolean',
        'acesso_vitrine_gmc' => 'boolean',
        'medico_celebridade' => 'boolean'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
