<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'nome',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'file_web_view',
        'token',
        'sign_url'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get icon class based on file type
     */
    public function getIconClassAttribute()
    {
        if (!$this->file_type) {
            return 'fas fa-file text-muted';
        }

        return match(strtolower($this->file_type)) {
            'pdf', 'application/pdf' => 'fas fa-file-pdf text-danger',
            'doc', 'docx', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-primary',
            'xls', 'xlsx', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel text-success',
            'jpg', 'jpeg', 'png', 'gif', 'image/jpeg', 'image/png', 'image/gif' => 'fas fa-file-image text-info',
            'zip', 'rar', 'application/zip', 'application/x-rar-compressed' => 'fas fa-file-archive text-dark',
            default => 'fas fa-file text-muted'
        };
    }

    /**
     * Get download URL
     */
    public function getDownloadUrl()
    {
        // Se tiver file_web_view, retornar
        if ($this->file_web_view) {
            return $this->file_web_view;
        }

        // Se tiver file_path, gerar URL de storage
        if ($this->file_path) {
            return \Storage::url($this->file_path);
        }

        return null;
    }
}
