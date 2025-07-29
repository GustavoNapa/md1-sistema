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
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
        'unread_count' => 'integer',
        'metadata' => 'array',
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
     * Relacionamento com Links de Auditoria
     */
    public function links(): HasMany
    {
        return $this->hasMany(ConversationLink::class, 'conversation_id');
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
     * Normalizar número de telefone
     */
    public static function normalizePhone($phone)
    {
        // Remove todos os caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Se não começar com 55, adiciona o código do Brasil
        if (!str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }

    /**
     * Buscar ou criar conversa por telefone
     */
    public static function findOrCreateByPhone($phone, $instanceName, $contactName = null)
    {
        $normalizedPhone = self::normalizePhone($phone);
        
        $conversation = self::where('contact_phone', $normalizedPhone)
            ->where('instance_name', $instanceName)
            ->first();
            
        if (!$conversation) {
            $conversation = self::create([
                'contact_phone' => $normalizedPhone,
                'contact_name' => $contactName,
                'instance_name' => $instanceName,
                'last_message_at' => now(),
            ]);
            
            // Tentar associar automaticamente
            $conversation->autoAssociate();
        }
        
        return $conversation;
    }

    /**
     * Tentar associar automaticamente com cliente
     */
    public function autoAssociate()
    {
        return \App\Services\ConversationLinker::autoAssociate($this);
    }

    /**
     * Obter nome para exibição
     */
    public function getDisplayNameAttribute()
    {
        $info = \App\Services\ConversationLinker::getAssociationInfo($this);
        return $info['name'];
    }

    /**
     * Obter informações de associação
     */
    public function getAssociationInfoAttribute()
    {
        return \App\Services\ConversationLinker::getAssociationInfo($this);
    }

    /**
     * Verificar se está associada
     */
    public function isAssociated(): bool
    {
        return $this->client_id !== null || $this->contact_id !== null;
    }

    /**
     * Obter possíveis matches para associação
     */
    public function getPossibleMatches()
    {
        return \App\Services\ConversationLinker::findPossibleMatches($this->contact_phone);
    }

    /**
     * Marcar mensagens como lidas
     */
    public function markAsRead()
    {
        $this->update(['unread_count' => 0]);
        
        $this->messages()
            ->where('direction', 'inbound')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
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
