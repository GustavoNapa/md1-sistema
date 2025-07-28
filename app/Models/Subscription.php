<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ["name", "status"];

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }
}


