<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'cep',
        'endereco',
        'numero_casa',
        'complemento',
        'bairro',
        'cidade',
        'estado'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

