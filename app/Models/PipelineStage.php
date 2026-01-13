<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipeline_id',
        'name',
        'order',
        'color',
        'type'
    ];

    // Relacionamentos
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class)->orderBy('stage_order');
    }

    // Métodos auxiliares
    public function isWinStage()
    {
        return $this->type === 'ganho';
    }

    public function isLostStage()
    {
        return $this->type === 'perdido';
    }
}
