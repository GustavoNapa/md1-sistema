<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'message_id',
        'direction',
        'type',
        'content',
        'media',
        'from_phone',
        'to_phone',
        'user_id',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'raw_data',
    ];

    protected $casts = [
        'media' => 'array',
        'raw_data' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    /**
     * Relacionamento com Conversa
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsappConversation::class, 'conversation_id');
    }

    /**
     * Relacionamento com Usuário (quem enviou)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para mensagens de entrada
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope para mensagens de saída
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope para mensagens não lidas
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Verificar se é mensagem de entrada
     */
    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }

    /**
     * Verificar se é mensagem de saída
     */
    public function isOutbound(): bool
    {
        return $this->direction === 'outbound';
    }

    /**
     * Obter ícone do status
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => '🕒',
            'sent' => '✓',
            'delivered' => '✓✓',
            'read' => '✓✓',
            'failed' => '❗',
            default => '?'
        };
    }

    /**
     * Obter classe CSS do status
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'text-warning',
            'sent' => 'text-info',
            'delivered' => 'text-success',
            'read' => 'text-primary',
            'failed' => 'text-danger',
            default => 'text-muted'
        };
    }

    /**
     * Obter label do tipo de mensagem
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'text' => 'Texto',
            'image' => 'Imagem',
            'audio' => 'Áudio',
            'video' => 'Vídeo',
            'document' => 'Documento',
            'location' => 'Localização',
            'contact' => 'Contato',
            'sticker' => 'Figurinha',
            default => 'Desconhecido'
        };
    }

    /**
     * Marcar como lida
     */
    public function markAsRead()
    {
        if ($this->isInbound() && !$this->read_at) {
            $this->update(['read_at' => now()]);
            
            // Decrementar contador da conversa
            $this->conversation->decrement('unread_count');
        }
    }

    /**
     * Criar mensagem a partir de webhook
     */
    public static function createFromWebhook(array $data, WhatsappConversation $conversation)
    {
        return self::create([
            'conversation_id' => $conversation->id,
            'message_id' => $data['key']['id'] ?? uniqid(),
            'direction' => 'inbound',
            'type' => $data['messageType'] ?? 'text',
            'content' => $data['message']['conversation'] ?? $data['message']['text'] ?? null,
            'media' => isset($data['message']['media']) ? $data['message']['media'] : null,
            'from_phone' => $data['key']['remoteJid'] ?? '',
            'to_phone' => $data['instanceName'] ?? '',
            'status' => 'delivered',
            'sent_at' => now(),
            'delivered_at' => now(),
            'raw_data' => $data,
        ]);
    }
}
