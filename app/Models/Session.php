<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'preceptor_record_id',
        'numero_sessao',
        'fase',
        'tipo',
        'semana_mes',
        'data_agendada',
        'data_realizada',
        'status',
        'confirmou_24h',
        'medico_confirmou',
        'motivo_desmarcou',
        'medico_compareceu',
        'status_reagendamento',
        'data_remarcada',
        'observacoes',
        'resultado',
        'media_mensal_antes',
        'meta_mensal_desejada'
    ];

    protected $casts = [
        'data_agendada' => 'datetime',
        'data_realizada' => 'datetime',
        'data_remarcada' => 'date',
        'confirmou_24h' => 'boolean',
        'medico_compareceu' => 'boolean',
        'media_mensal_antes' => 'decimal:2',
        'meta_mensal_desejada' => 'decimal:2'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function preceptorRecord()
    {
        return $this->belongsTo(PreceptorRecord::class);
    }
}
