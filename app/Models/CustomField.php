<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    protected $fillable = [
        'field_group_id',
        'name',
        'identifier',
        'type',
        'options',
        'order',
        'is_system',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'is_system' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function fieldGroup(): BelongsTo
    {
        return $this->belongsTo(FieldGroup::class);
    }

    public function leadValues(): HasMany
    {
        return $this->hasMany(LeadCustomFieldValue::class);
    }
}
