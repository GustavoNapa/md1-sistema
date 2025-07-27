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
        'achieved_at',
        'achievement_type_id'
    ];

    protected $casts = [
        'achieved_at' => 'date'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
    public function achievementType()
    {
        return $this->belongsTo(AchievementType::class, 'achievement_type_id');
    }
}
