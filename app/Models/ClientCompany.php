<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'cnpj',
        'type',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'website',
        'is_main',
        'notes'
    ];

    protected $casts = [
        'is_main' => 'boolean'
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
            'clinic' => 'Clínica',
            'laboratory' => 'Laboratório',
            'hospital' => 'Hospital',
            'office' => 'Consultório',
            'other' => 'Outro'
        ];

        return $labels[$this->type] ?? 'Outro';
    }

    public function getFormattedCnpjAttribute(): ?string
    {
        if (!$this->cnpj) {
            return null;
        }

        $cnpj = preg_replace('/[^0-9]/', '', $this->cnpj);
        
        if (strlen($cnpj) == 14) {
            return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . 
                   substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
        }
        
        return $this->cnpj;
    }

    public function getFormattedZipCodeAttribute(): ?string
    {
        if (!$this->zip_code) {
            return null;
        }

        $zipCode = preg_replace('/[^0-9]/', '', $this->zip_code);
        
        if (strlen($zipCode) == 8) {
            return substr($zipCode, 0, 5) . '-' . substr($zipCode, 5, 3);
        }
        
        return $this->zip_code;
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        } elseif (strlen($phone) == 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
        }
        
        return $this->phone;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->formatted_zip_code
        ]);

        return implode(', ', $parts);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_main ? 'Principal' : 'Secundária';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_main ? 'bg-primary' : 'bg-secondary';
    }

    // Scopes
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByState($query, string $state)
    {
        return $query->where('state', $state);
    }
}

