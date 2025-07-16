<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'diagnosis',
        'notes',
        'date'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
