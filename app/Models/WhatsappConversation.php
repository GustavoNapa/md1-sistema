<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_phone',
        'contact_name',
        'instance_name',
        'client_id',
        'contact_id',
        'user_id',
        'unread_count',
        'last_message_at',
        'is_active',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
        'unread_count' => 'integer',
    ];

    /**
     * Relacionamento com Cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relacionamento com Usuário (atendente)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Contato específico
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(ClientPhone::class, 'contact_id');
    }

    /**
     * Relacionamento com Mensagens
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'conversation_id');
    }

    /**
     * Scope para conversas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para conversas com mensagens não lidas
     */
    public function scopeWithUnread($query)
    {
        return $query->where('unread_count', '>', 0);
    }

    /**
     * Scope para conversas de um atendente específico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Normaliza o telefone para formato padrão
     */
    public static function normalizePhone($phone)
    {
        // Remove todos os caracteres não numéricos
        $phone = preg_replace('/\D/', '', $phone);
        
        // Se começar com 55 (Brasil), mantém
        if (strlen($phone) >= 13 && substr($phone, 0, 2) === '55') {
            return $phone;
        }
        
        // Se não tem código do país, adiciona 55
        if (strlen($phone) === 11) {
            return '55' . $phone;
        }
        
        return $phone;
    }

    /**
     * Marca conversa como lida
     */
    public function markAsRead()
    {
        $this->update(['unread_count' => 0]);
    }

    /**
     * Incrementa contador de não lidas
     */
    public function incrementUnread()
    {
        $this->increment('unread_count');
        $this->touch('last_message_at');
    }

    /**
     * Atualiza timestamp da última mensagem
     */
    public function updateLastMessage()
    {
        $this->touch('last_message_at');
    }

    /**
     * Verifica se a conversa está vinculada a um cliente
     */
    public function isLinked(): bool
    {
        return !is_null($this->client_id);
    }

    /**
     * Obtém o nome de exibição da conversa
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->contact_name ?: $this->contact_phone;
    }
}

