<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'event_type',
        'description',
        'event_date'
    ];

    protected $casts = [
        'event_date' => 'datetime'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
