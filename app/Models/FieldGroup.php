<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldGroup extends Model
{
    protected $fillable = [
        'name',
        'type',
        'order',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class)->orderBy('order');
    }
}
