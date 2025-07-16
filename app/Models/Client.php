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
        'active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
        'media_faturamento' => 'decimal:2'
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

    // Accessors para exibição
    public function getFormattedCpfAttribute()
    {
        $cpf = $this->cpf;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
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
        return $this->active ? 'Ativo' : 'Inativo';
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
