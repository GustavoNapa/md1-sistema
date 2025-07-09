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
        'active' => 'boolean'
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
}
