<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'action',
        'description',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Relacionamentos
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método helper para criar histórico
    public static function logAction($leadId, $action, $description, $changes = null, $userId = null)
    {
        return self::create([
            'lead_id' => $leadId,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'changes' => $changes,
        ]);
    }

    // Método para obter ícone baseado na ação
    public function getIconAttribute()
    {
        return match($this->action) {
            'created' => 'fas fa-plus-circle',
            'updated' => 'fas fa-edit',
            'stage_changed' => 'fas fa-exchange-alt',
            'pipeline_changed' => 'fas fa-sitemap',
            'assigned' => 'fas fa-user-check',
            'unassigned' => 'fas fa-user-times',
            'archived' => 'fas fa-archive',
            'unarchived' => 'fas fa-box-open',
            'deleted' => 'fas fa-trash',
            default => 'fas fa-circle',
        };
    }

    // Método para obter cor baseado na ação
    public function getColorAttribute()
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'stage_changed' => 'primary',
            'pipeline_changed' => 'warning',
            'assigned' => 'success',
            'unassigned' => 'secondary',
            'archived' => 'secondary',
            'unarchived' => 'info',
            'deleted' => 'danger',
            default => 'secondary',
        };
    }
}
