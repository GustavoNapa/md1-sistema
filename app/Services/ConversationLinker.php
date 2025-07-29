<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientPhone;
use App\Models\ConversationLink;
use App\Models\WhatsappConversation;
use Illuminate\Support\Collection;

class ConversationLinker
{
    /**
     * Normaliza o número de telefone para um formato padrão.
     */
    public function normalizePhone(string $phone): string
    {
        // Remove todos os caracteres não numéricos
        $phone = preg_replace("/\\D/", "", $phone);

        // Se o número já começar com 55 (código do Brasil) e tiver 13 dígitos (55 + DDD + 9 dígitos), retorna como está.
        if (strlen($phone) === 13 && substr($phone, 0, 2) === "55") {
            return $phone;
        }

        // Se tiver 11 dígitos (DDD + 9 dígitos), assume que é Brasil e adiciona 55.
        if (strlen($phone) === 11) {
            return "55" . $phone;
        }

        // Se tiver 10 dígitos (DDD + 8 dígitos), assume que é Brasil e adiciona 559 (para números antigos sem o 9).
        if (strlen($phone) === 10) {
            return "559" . $phone; // Isso pode precisar de ajuste dependendo da regra de negócio
        }

        return $phone;
    }

    /**
     * Busca clientes e contatos que correspondem a um número de telefone.
     */
    public function findMatches(string $phone): Collection
    {
        $normalizedPhone = $this->normalizePhone($phone);

        // Busca por clientes que tenham um telefone principal correspondente
        $clientsByPhone = Client::whereHas("phones", function ($query) use ($normalizedPhone) {
            $query->where("phone", $normalizedPhone)->where("is_primary", true);
        })->get();

        // Busca por contatos (ClientPhone) que correspondem ao número
        $contacts = ClientPhone::where("phone", $normalizedPhone)->with("client")->get();

        // Combina os resultados, garantindo que não haja duplicatas de clientes
        $allMatches = new Collection();

        foreach ($clientsByPhone as $client) {
            $allMatches->put($client->id, $client);
        }

        foreach ($contacts as $contact) {
            if ($contact->client) {
                $allMatches->put($contact->client->id, $contact->client);
            }
        }

        return $allMatches->values();
    }

    /**
     * Associa uma conversa a um cliente.
     */
    public function associate(WhatsappConversation $conversation, int $clientId, ?int $userId = null): void
    {
        $oldClientId = $conversation->client_id;
        $oldContactId = $conversation->contact_id;

        $conversation->update([
            "client_id" => $clientId,
            "contact_id" => null, // Reset contact_id if associating with a client
        ]);

        ConversationLink::create([
            "whatsapp_conversation_id" => $conversation->id,
            "old_client_id" => $oldClientId,
            "new_client_id" => $clientId,
            "old_contact_id" => $oldContactId,
            "new_contact_id" => null,
            "linked_by_user_id" => $userId,
            "action" => "associate_client",
        ]);
    }

    /**
     * Desvincula uma conversa de um cliente/contato.
     */
    public function unlink(WhatsappConversation $conversation, ?int $userId = null): void
    {
        $oldClientId = $conversation->client_id;
        $oldContactId = $conversation->contact_id;

        $conversation->update([
            "client_id" => null,
            "contact_id" => null,
        ]);

        ConversationLink::create([
            "whatsapp_conversation_id" => $conversation->id,
            "old_client_id" => $oldClientId,
            "new_client_id" => null,
            "old_contact_id" => $oldContactId,
            "new_contact_id" => null,
            "linked_by_user_id" => $userId,
            "action" => "unlink",
        ]);
    }

    /**
     * Associa uma conversa a um contato específico (ClientPhone).
     */
    public function associateContact(WhatsappConversation $conversation, int $contactId, ?int $userId = null): void
    {
        $oldClientId = $conversation->client_id;
        $oldContactId = $conversation->contact_id;

        $contactPhone = ClientPhone::findOrFail($contactId);

        $conversation->update([
            "client_id" => $contactPhone->client_id,
            "contact_id" => $contactId,
        ]);

        ConversationLink::create([
            "whatsapp_conversation_id" => $conversation->id,
            "old_client_id" => $oldClientId,
            "new_client_id" => $contactPhone->client_id,
            "old_contact_id" => $oldContactId,
            "new_contact_id" => $contactId,
            "linked_by_user_id" => $userId,
            "action" => "associate_contact",
        ]);
    }
}


