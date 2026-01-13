<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pipeline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'color',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Relacionamentos
    public function stages()
    {
        return $this->hasMany(PipelineStage::class)->orderBy('order');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    // Métodos auxiliares
    public function setAsDefault()
    {
        // Remove o padrão de todos os outros pipelines do mesmo tipo
        static::where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }

    public static function getDefault($type = 'leads')
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->first();
    }
}
