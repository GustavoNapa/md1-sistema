<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'especialidade',
        'cidade_atendimento',
        'uf',
        'regiao',
        'instagram',
        'email',
        'telefone',
        'ativo'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ativo' => 'boolean'
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
}
