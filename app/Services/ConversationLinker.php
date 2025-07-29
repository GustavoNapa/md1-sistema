<?php

namespace App\Services;

use App\Models\Client;
use App\Models\WhatsappConversation;
use App\Models\ConversationLink;
use Illuminate\Support\Collection;

class ConversationLinker
{
    /**
     * Normalizar número de telefone para formato padrão
     */
    public static function normalizePhone(string $phone): string
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
     * Buscar possíveis matches para um número de telefone
     */
    public static function findPossibleMatches(string $phone): Collection
    {
        $normalizedPhone = self::normalizePhone($phone);
        $matches = collect();
        
        // Buscar em clientes
        $clients = Client::where('phone', $normalizedPhone)
            ->orWhere('phone', 'LIKE', '%' . substr($normalizedPhone, -9))
            ->orWhere('phone', 'LIKE', '%' . substr($normalizedPhone, -10))
            ->orWhere('phone', 'LIKE', '%' . substr($normalizedPhone, -11))
            ->get();
            
        foreach ($clients as $client) {
            $matches->push([
                'type' => 'client',
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email ?? '',
                'model' => $client,
            ]);
        }
        
        // TODO: Buscar em contatos quando a tabela existir
        // $contacts = Contact::where('telefone', $normalizedPhone)->get();
        
        return $matches;
    }

    /**
     * Associar automaticamente uma conversa
     */
    public static function autoAssociate(WhatsappConversation $conversation): bool
    {
        if ($conversation->client_id || $conversation->contact_id) {
            return true; // Já está associada
        }
        
        $matches = self::findPossibleMatches($conversation->contact_phone);
        
        if ($matches->count() === 1) {
            // Apenas um match - associar automaticamente
            $match = $matches->first();
            
            if ($match['type'] === 'client') {
                $conversation->update(['client_id' => $match['id']]);
                return true;
            }
            
            // TODO: Implementar para contatos
            // if ($match['type'] === 'contact') {
            //     $conversation->update(['contact_id' => $match['id']]);
            //     return true;
            // }
        }
        
        return false; // Múltiplos matches ou nenhum match
    }

    /**
     * Associar manualmente uma conversa
     */
    public static function manualAssociate(
        WhatsappConversation $conversation,
        string $type,
        int $id,
        int $userId,
        string $reason = null
    ): bool {
        // Salvar estado anterior para auditoria
        $oldType = null;
        $oldId = null;
        
        if ($conversation->client_id) {
            $oldType = 'client';
            $oldId = $conversation->client_id;
        } elseif ($conversation->contact_id) {
            $oldType = 'contact';
            $oldId = $conversation->contact_id;
        }
        
        // Validar o novo vínculo
        if ($type === 'client') {
            $client = Client::find($id);
            if (!$client) {
                return false;
            }
            
            $conversation->update([
                'client_id' => $id,
                'contact_id' => null,
            ]);
        } elseif ($type === 'contact') {
            // TODO: Implementar quando tabela contacts existir
            return false;
        } else {
            return false;
        }
        
        // Registrar log de auditoria
        ConversationLink::logChange(
            $conversation,
            $oldType,
            $oldId,
            $type,
            $id,
            \App\Models\User::find($userId),
            $reason
        );
        
        return true;
    }

    /**
     * Desassociar uma conversa
     */
    public static function unlink(
        WhatsappConversation $conversation,
        int $userId,
        string $reason = null
    ): bool {
        // Salvar estado anterior para auditoria
        $oldType = null;
        $oldId = null;
        
        if ($conversation->client_id) {
            $oldType = 'client';
            $oldId = $conversation->client_id;
        } elseif ($conversation->contact_id) {
            $oldType = 'contact';
            $oldId = $conversation->contact_id;
        }
        
        if (!$oldType) {
            return false; // Já está desassociada
        }
        
        $conversation->update([
            'client_id' => null,
            'contact_id' => null,
        ]);
        
        // Registrar log de auditoria
        ConversationLink::logChange(
            $conversation,
            $oldType,
            $oldId,
            null,
            null,
            \App\Models\User::find($userId),
            $reason
        );
        
        return true;
    }

    /**
     * Obter informações de associação para exibição
     */
    public static function getAssociationInfo(WhatsappConversation $conversation): array
    {
        if ($conversation->client_id && $conversation->client) {
            return [
                'type' => 'client',
                'id' => $conversation->client->id,
                'name' => $conversation->client->name,
                'email' => $conversation->client->email,
                'phone' => $conversation->client->phone,
                'is_associated' => true,
            ];
        }
        
        if ($conversation->contact_id) {
            // TODO: Implementar quando tabela contacts existir
            return [
                'type' => 'contact',
                'id' => $conversation->contact_id,
                'name' => 'Contato',
                'email' => '',
                'phone' => $conversation->contact_phone,
                'is_associated' => true,
            ];
        }
        
        return [
            'type' => null,
            'id' => null,
            'name' => $conversation->contact_name ?? 'Contato não identificado',
            'email' => '',
            'phone' => $conversation->contact_phone,
            'is_associated' => false,
        ];
    }
}

