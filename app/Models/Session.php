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
        'meta_mensal_desejada',
        // Novos campos de implementação e desenvolvimento
        'implementacao_fase',
        'impacto_faturamento',
        'dificuldades_travas',
        'desenvolvimento_ultima_preceptoria',
        'avancos_importantes',
        'momento_depoimento',
        'conseguiu_indicacao',
        'detalhes_indicacao',
        // Campos de faturamento
        'faturamento_mes_ano',
        'faturamento_valor',
        'faturamento_data_vencimento',
        'faturamento_status',
        'faturamento_observacoes',
    ];

    protected $casts = [
        'data_agendada' => 'datetime',
        'data_realizada' => 'datetime',
        'data_remarcada' => 'date',
        'confirmou_24h' => 'boolean',
        'medico_compareceu' => 'boolean',
        'media_mensal_antes' => 'decimal:2',
        'meta_mensal_desejada' => 'decimal:2',
        // Novos campos
        'conseguiu_indicacao' => 'boolean',
        'faturamento_valor' => 'decimal:2',
        'faturamento_data_vencimento' => 'date',
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
