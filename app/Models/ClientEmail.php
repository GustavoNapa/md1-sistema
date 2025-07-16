<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'email',
        'type',
        'is_primary',
        'is_verified',
        'notes'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean'
    ];

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'personal' => 'Pessoal',
            'work' => 'Trabalho',
            'other' => 'Outro'
        ];

        return $labels[$this->type] ?? 'Outro';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_primary) {
            return 'Principal';
        }

        return $this->is_verified ? 'Verificado' : 'Não verificado';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_primary) {
            return 'bg-primary';
        }

        return $this->is_verified ? 'bg-success' : 'bg-warning';
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

