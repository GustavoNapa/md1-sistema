<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'title',
        'description',
        'achieved_at'
    ];

    protected $casts = [
        'achieved_at' => 'date'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
