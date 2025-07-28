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

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}


