<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InscriptionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'title',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'external_url',
        'category',
        'description',
        'is_required',
        'is_verified',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    // Relacionamentos
    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessors
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'contrato' => 'Contrato',
            'documento_pessoal' => 'Documento Pessoal',
            'certificado' => 'Certificado',
            'comprovante_pagamento' => 'Comprovante de Pagamento',
            'material_curso' => 'Material do Curso',
            'outros' => 'Outros',
            default => 'Não definido'
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'upload' => 'Arquivo',
            'link' => 'Link Externo',
            default => 'Não definido'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_verified) {
            return 'Verificado';
        }
        
        if ($this->is_required) {
            return 'Pendente (Obrigatório)';
        }
        
        return 'Pendente';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_verified) {
            return 'bg-success';
        }
        
        if ($this->is_required) {
            return 'bg-warning';
        }
        
        return 'bg-secondary';
    }

    // Métodos utilitários
    public function getDownloadUrl(): ?string
    {
        if ($this->type === 'link') {
            return $this->external_url;
        }
        
        if ($this->type === 'upload' && $this->file_path) {
            return route('documents.download', [
                'inscription' => $this->inscription_id,
                'document' => $this->id
            ]);
        }
        
        return null;
    }

    public function getFileUrl(): ?string
    {
        if ($this->type === 'upload' && $this->file_path) {
            return Storage::url($this->file_path);
        }
        
        return null;
    }

    public function isImage(): bool
    {
        if (!$this->mime_type) {
            return false;
        }
        
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getIconClass(): string
    {
        if ($this->type === 'link') {
            return 'fas fa-external-link-alt text-primary';
        }
        
        if ($this->isPdf()) {
            return 'fas fa-file-pdf text-danger';
        }
        
        if ($this->isImage()) {
            return 'fas fa-file-image text-info';
        }
        
        return match($this->mime_type) {
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-primary',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel text-success',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fas fa-file-powerpoint text-warning',
            'text/plain' => 'fas fa-file-alt text-secondary',
            'application/zip', 'application/x-rar-compressed' => 'fas fa-file-archive text-dark',
            default => 'fas fa-file text-muted'
        };
    }

    // Scopes
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeUploads($query)
    {
        return $query->where('type', 'upload');
    }

    public function scopeLinks($query)
    {
        return $query->where('type', 'link');
    }
}

