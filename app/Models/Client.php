<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'email',
        'sexo',
        'media_faturamento',
        'doctoralia',
        'birth_date',
        'specialty',
        'service_city',
        'state',
        'region',
        'instagram',
        'phone',
        'active',
        'status',
        'pause_start_date',
        'pause_end_date',
        'pause_reason',
        'phase',
        'phase_start_date',
        'phase_week'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
        'media_faturamento' => 'decimal:2',
        'pause_start_date' => 'date',
        'pause_end_date' => 'date',
        'phase_start_date' => 'date',
        'phase_week' => 'integer'
    ];

    // Relacionamentos
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function whatsappMessages()
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    public function emails()
    {
        return $this->hasMany(ClientEmail::class);
    }

    public function phones()
    {
        return $this->hasMany(ClientPhone::class);
    }

    public function companies()
    {
        return $this->hasMany(ClientCompany::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Accessors para exibição
    public function getFormattedCpfAttribute()
    {
        // Normaliza removendo tudo que não for dígito e formata como ###.###.###-##
        $cpf = preg_replace('/\D/', '', $this->cpf);
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }

        // Se não houver 11 dígitos, retorna o CPF original (possivelmente vazio) sem alterações
        return $this->cpf;
    }

    // Mutator: armazena somente os dígitos do CPF
    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = $value ? preg_replace('/\D/', '', $value) : null;
    }

    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        }
        return $this->phone;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'paused' => 'Em Pausa'
        ];
        
        return $labels[$this->status ?? 'active'] ?? ($this->active ? 'Ativo' : 'Inativo');
    }

    /**
     * Retorna lista de opções de status
     */
    public static function getStatusOptions()
    {
        return [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'paused' => 'Em Pausa'
        ];
    }

    /**
     * Retorna lista de opções de fases
     */
    public static function getPhaseOptions()
    {
        return [
            'fase_1' => 'Fase 1 - Inicial',
            'fase_2' => 'Fase 2 - Desenvolvimento',
            'fase_3' => 'Fase 3 - Consolidação',
            'fase_4' => 'Fase 4 - Avançado',
            'concluido' => 'Concluído'
        ];
    }

    /**
     * Calcula a semana atual baseado na data de início da fase
     */
    public function calculatePhaseWeek()
    {
        if (!$this->phase_start_date) {
            return null;
        }

        $startDate = $this->phase_start_date;
        $currentDate = now();
        
        // Calcula a diferença em semanas
        $weeksDiff = $startDate->diffInWeeks($currentDate);
        
        // Retorna a semana atual (1-27)
        return min($weeksDiff + 1, 27);
    }

    /**
     * Verifica se o cliente está em pausa
     */
    public function isPaused()
    {
        return $this->status === 'paused';
    }

    /**
     * Retorna o tempo restante de pausa em dias
     */
    public function getRemainingPauseDays()
    {
        if (!$this->isPaused() || !$this->pause_end_date) {
            return null;
        }

        $endDate = $this->pause_end_date;
        $now = now();

        if ($endDate->isFuture()) {
            return $now->diffInDays($endDate);
        }

        return 0;
    }

    public function getSexoLabelAttribute()
    {
        $labels = [
            'masculino' => 'Masculino',
            'feminino' => 'Feminino',
            'outro' => 'Outro',
            'nao_informado' => 'Não Informado'
        ];

        return $labels[$this->sexo] ?? 'Não Informado';
    }

    public function getFormattedMediaFaturamentoAttribute()
    {
        if (!$this->media_faturamento) {
            return 'Não informado';
        }

        return 'R$ ' . number_format($this->media_faturamento, 2, ',', '.');
    }
}
