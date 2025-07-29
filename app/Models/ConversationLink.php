<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\BelongsTo;
>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600

class ConversationLink extends Model
{
    use HasFactory;
<<<<<<< HEAD
=======

    protected $fillable = [
        'conversation_id',
        'old_type',
        'old_id',
        'new_type',
        'new_id',
        'user_id',
        'reason',
    ];

    /**
     * Relacionamento com Conversa
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsappConversation::class, 'conversation_id');
    }

    /**
     * Relacionamento com Usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obter o registro anterior (polimórfico)
     */
    public function oldRecord()
    {
        if ($this->old_type === 'client') {
            return Client::find($this->old_id);
        }
        
        // Se existir tabela contacts
        // if ($this->old_type === 'contact') {
        //     return Contact::find($this->old_id);
        // }
        
        return null;
    }

    /**
     * Obter o novo registro (polimórfico)
     */
    public function newRecord()
    {
        if ($this->new_type === 'client') {
            return Client::find($this->new_id);
        }
        
        // Se existir tabela contacts
        // if ($this->new_type === 'contact') {
        //     return Contact::find($this->new_id);
        // }
        
        return null;
    }

    /**
     * Criar log de alteração
     */
    public static function logChange(
        WhatsappConversation $conversation,
        $oldType,
        $oldId,
        $newType,
        $newId,
        User $user,
        $reason = null
    ) {
        return self::create([
            'conversation_id' => $conversation->id,
            'old_type' => $oldType,
            'old_id' => $oldId,
            'new_type' => $newType,
            'new_id' => $newId,
            'user_id' => $user->id,
            'reason' => $reason,
        ]);
    }
>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600
}
