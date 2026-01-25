<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        "subscription_id",
        "description",
        "release_date",
        "expiration_date",
    ];

    protected $casts = [
        "release_date" => "date",
        "expiration_date" => "date",
    ];

    /**
     * Relacionamento com Inscription
     * subscription_id na verdade referencia inscriptions.id
     */
    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'subscription_id');
    }
    
    // Alias para compatibilidade
    public function subscription()
    {
        return $this->inscription();
    }
}


