<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'notes',
        'follow_up_date',
        'status'
    ];

    protected $casts = [
        'follow_up_date' => 'date'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
