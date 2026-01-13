<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'is_whatsapp',
        'email',
        'origin',
        'origin_other',
        'notes',
        'pipeline_id',
        'pipeline_stage_id',
        'user_id',
        'stage_order',
        'is_archived'
    ];

    protected $casts = [
        'is_whatsapp' => 'boolean',
        'is_archived' => 'boolean',
    ];

    // Relacionamentos
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage()
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Acessores
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return null;
        }
        
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
        } elseif (strlen($phone) == 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
        }
        
        return $this->phone;
    }

    public function getOriginLabelAttribute()
    {
        if ($this->origin === 'outro' && $this->origin_other) {
            return $this->origin_other;
        }

        $origins = [
            'campanha' => 'Campanha',
            'email' => 'Email',
            'facebook' => 'Facebook',
            'indicacao' => 'Indicação',
            'instagram' => 'Instagram',
            'whatsapp' => 'WhatsApp',
            'outro' => 'Outro'
        ];

        return $origins[$this->origin] ?? $this->origin;
    }

    // Métodos auxiliares
    public function moveToStage($stageId)
    {
        $stage = PipelineStage::findOrFail($stageId);
        
        // Verifica se a etapa pertence ao mesmo pipeline
        if ($stage->pipeline_id !== $this->pipeline_id) {
            throw new \Exception('A etapa não pertence ao mesmo pipeline do lead.');
        }

        // Atualiza a ordem do lead na nova etapa
        $maxOrder = static::where('pipeline_stage_id', $stageId)->max('stage_order') ?? 0;

        $this->update([
            'pipeline_stage_id' => $stageId,
            'stage_order' => $maxOrder + 1
        ]);
    }

    public function archive()
    {
        $this->update(['is_archived' => true]);
    }

    public function restore()
    {
        $this->update(['is_archived' => false]);
    }

    public function assignToPipeline($pipelineId)
    {
        $pipeline = Pipeline::findOrFail($pipelineId);
        $firstStage = $pipeline->stages()->orderBy('order')->first();
        
        if (!$firstStage) {
            throw new \Exception('O pipeline não possui etapas configuradas.');
        }

        $maxOrder = static::where('pipeline_stage_id', $firstStage->id)->max('stage_order') ?? 0;

        $this->update([
            'pipeline_id' => $pipelineId,
            'pipeline_stage_id' => $firstStage->id,
            'stage_order' => $maxOrder + 1
        ]);
    }

    // Scopes
    public function scopeWithoutPipeline($query)
    {
        return $query->whereNull('pipeline_id')->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}
