<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'vendor_id',
        'produto',
        'turma',
        'status',
        'classificacao',
        'medboss',
        'crmb',
        'data_inicio',
        'data_termino_original',
        'data_termino_real',
        'data_liberacao_plataforma',
        'semana_calendario',
        'semana_real',
        'valor_pago',
        'forma_pagamento',
        'obs_comercial',
        'obs_geral',
        'motivo_alteracao_data'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_termino_original' => 'date',
        'data_termino_real' => 'date',
        'data_liberacao_plataforma' => 'date',
        'medboss' => 'boolean',
        'valor_pago' => 'decimal:2'
    ];

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function preceptorRecord()
    {
        return $this->hasOne(PreceptorRecord::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function onboardingEvents()
    {
        return $this->hasMany(OnboardingEvent::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
