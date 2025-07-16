<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZapsignDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'client_id',
        'template_mapping_id',
        'zapsign_document_id',
        'zapsign_token',
        'external_id',
        'name',
        'status',
        'original_file_url',
        'signed_file_url',
        'webhook_data',
        'signed_at',
        'expires_at',
    ];

    protected $casts = [
        'webhook_data' => 'array',
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the inscription that owns the document.
     */
    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * Get the client that owns the document.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the template mapping used for this document.
     */
    public function templateMapping(): BelongsTo
    {
        return $this->belongsTo(ZapsignTemplateMapping::class, 'template_mapping_id');
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pendente',
            'signed' => 'Assinado',
            'expired' => 'Expirado',
            'cancelled' => 'Cancelado',
            'error' => 'Erro',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the status badge class for display.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'pending' => 'bg-warning',
            'signed' => 'bg-success',
            'expired' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'error' => 'bg-danger',
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Check if the document is signed.
     */
    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    /**
     * Check if the document is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the document has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Get the download URL for the signed document.
     */
    public function getSignedDocumentUrl(): ?string
    {
        return $this->signed_file_url;
    }

    /**
     * Get the download URL for the original document.
     */
    public function getOriginalDocumentUrl(): ?string
    {
        return $this->original_file_url;
    }

    /**
     * Scope for documents with specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for signed documents.
     */
    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    /**
     * Scope for pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for documents by inscription.
     */
    public function scopeForInscription($query, int $inscriptionId)
    {
        return $query->where('inscription_id', $inscriptionId);
    }

    /**
     * Scope for documents by client.
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}

