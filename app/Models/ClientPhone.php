<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'phone',
        'type',
        'is_whatsapp',
        'is_primary',
        'notes'
    ];

    protected $casts = [
        'is_whatsapp' => 'boolean',
        'is_primary' => 'boolean'
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
            'mobile' => 'Celular',
            'landline' => 'Fixo',
            'work' => 'Trabalho',
            'other' => 'Outro'
        ];

        return $labels[$this->type] ?? 'Outro';
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        } elseif (strlen($phone) == 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
        }
        
        return $this->phone;
    }

    public function getStatusLabelsAttribute(): array
    {
        $labels = [];
        
        if ($this->is_primary) {
            $labels[] = 'Principal';
        }
        
        if ($this->is_whatsapp) {
            $labels[] = 'WhatsApp';
        }
        
        return $labels;
    }

    public function getWhatsappLabelAttribute(): string
    {
        return $this->is_whatsapp ? 'Sim' : 'Não';
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeWhatsapp($query)
    {
        return $query->where('is_whatsapp', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

