<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Inscription;
use App\Models\Document;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks
     */
    public function handle(Request $request, string $action)
    {
        try {
            // Validar assinatura HMAC se configurada
            if (!$this->validateSignature($request)) {
                return response()->json([\"error\" => \"Invalid signature\"], 401);
            }

            // Log da requisição
            Log::info(\"Webhook recebido\", [
                \"action\" => $action,
                \"data\" => $request->all(),
                \"ip\" => $request->ip()
            ]);

            // Processar ação específica
            switch ($action) {
                case \"attach-document\":
                    return $this->attachDocument($request);
                
                case \"update-client\":
                    return $this->updateClient($request);
                
                case \"update-inscription\":
                    return $this->updateInscription($request);
                
                case \"send-whatsapp\":
                    return $this->sendWhatsappMessage($request);
                
                case \"whatsapp-message\":
                    return $this->handleWhatsappMessage($request);
                
                default:
                    return response()->json([\"error\" => \"Action not supported\"], 400);
            }

        } catch (\Exception $e) {
            Log::error(\"Erro ao processar webhook\", [
                \"action\" => $action,
                \"error\" => $e->getMessage(),
                \"trace\" => $e->getTraceAsString()
            ]);

            return response()->json([\"error\" => \"Internal server error\"], 500);
        }
    }

    /**
     * Anexar documento a uma inscrição
     */
    private function attachDocument(Request $request)
    {
        $validated = $request->validate([
            \"inscription_id\" => \"required|exists:inscriptions,id\",
            \"document_name\" => \"required|string|max:255\",
            \"document_type\" => \"required|string|max:100\",
            \"document_url\" => \"required|url\",
            \"file_size\" => \"nullable|integer\"
        ]);

        $inscription = Inscription::findOrFail($validated[\"inscription_id\"]);

        // Baixar arquivo da URL
        $fileContent = file_get_contents($validated[\"document_url\"]);
        $fileName = time() . \"_\" . $validated[\"document_name\"];
        $filePath = \"documents/{$inscription->id}/{$fileName}\";

        Storage::put($filePath, $fileContent);

        // Criar registro do documento
        $document = Document::create([
            \"inscription_id\" => $inscription->id,
            \"name\" => $validated[\"document_name\"],
            \"type\" => $validated[\"document_type\"],
            \"path\" => $filePath,
            \"size\" => $validated[\"file_size\"] ?? strlen($fileContent),
            \"upload_date\" => now()
        ]);

        return response()->json([
            \"success\" => true,
            \"message\" => \"Documento anexado com sucesso\",
            \"document_id\" => $document->id
        ]);
    }

    /**
     * Atualizar dados do cliente
     */
    private function updateClient(Request $request)
    {
        $validated = $request->validate([
            \"client_id\" => \"required|exists:clients,id\",
            \"data\" => \"required|array\"
        ]);

        $client = Client::findOrFail($validated[\"client_id\"]);
        
        // Filtrar apenas campos permitidos
        $allowedFields = [\"name\", \"email\", \"phone\", \"specialty\", \"service_city\", \""state\", \"region\", \"instagram\"];
        $updateData = array_intersect_key($validated[\"data\"], array_flip($allowedFields));

        $client->update($updateData);

        return response()->json([
            \"success\" => true,
            \"message\" => \"Cliente atualizado com sucesso\",
            \"client\" => $client->fresh()
        ]);
    }

    /**
     * Atualizar dados da inscrição
     */
    private function updateInscription(Request $request)
    {
        $validated = $request->validate([
            \"inscription_id\" => \"required|exists:inscriptions,id\",
            \"data\" => \"required|array\"
        ]);

        $inscription = Inscription::findOrFail($validated[\"inscription_id\"]);
        
        // Filtrar apenas campos permitidos
        $allowedFields = [
            \"status\", \"classification\", \"current_week\", \"calendar_week\",
            \"commercial_notes\", \"general_notes\", \"actual_end_date\"
        ];
        $updateData = array_intersect_key($validated[\"data\"], array_flip($allowedFields));

        $inscription->update($updateData);

        return response()->json([
            \"success\" => true,
            \"message\" => \"Inscrição atualizada com sucesso\",
            \"inscription\" => $inscription->fresh()
        ]);
    }

    /**
     * Enviar mensagem WhatsApp (placeholder)
     */
    private function sendWhatsappMessage(Request $request)
    {
        $validated = $request->validate([
            \"client_id\" => \"required|exists:clients,id\",
            \"message\" => \"required|string\",
            \"type\" => \"nullable|string|in:text,image,document\"
        ]);

        // TODO: Implementar integração com Evolution API
        Log::info(\"Mensagem WhatsApp solicitada\", $validated);

        return response()->json([
            \"success\" => true,
            \"message\" => \"Mensagem WhatsApp processada\",
            \"status\" => \"queued\"
        ]);
    }

    /**
     * Validar assinatura HMAC
     */
    private function validateSignature(Request $request): bool
    {
        $secret = config(\"app.webhook_secret\");
        
        if (!$secret) {
            return true; // Se não há secret configurado, pular validação
        }

        $signature = $request->header(\"X-MD1-Signature\");
        
        if (!$signature) {
            return false;
        }

        $expectedSignature = \"sha256=\" . hash_hmac(\"sha256\", $request->getContent(), $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Endpoint de teste para webhooks
     */
    public function test(Request $request)
    {
        return response()->json([
            \"success\" => true,
            \"message\" => \"Webhook endpoint funcionando\",
            \"timestamp\" => now()->toISOString(),
            \"received_data\" => $request->all()
        ]);
    }

    /**
     * Handle incoming WhatsApp messages from webhook.
     */
    private function handleWhatsappMessage(Request $request)
    {
        // Validar e extrair dados do payload da Evolution API
        $validated = $request->validate([
            \"instance.instanceName\" => \"required|string\",
            \"messages\" => \"required|array\",
            \"messages.*.key.remoteJid\" => \"required|string\",

            \"messages.*.key.fromMe\" => \"required|boolean\",
            \"messages.*.key.id\" => \"required|string\",
            \"messages.*.message.conversation\" => \"nullable|string\",
            \"messages.*.message.extendedTextMessage.text\" => \"nullable|string\",
            \"messages.*.messageType\" => \"required|string\",
            \"messages.*.pushName\" => \"nullable|string\",
            // Adicionar validações para outros tipos de mensagem (imagem, documento, etc.) conforme necessário
        ]);

        foreach ($validated[\"messages\"] as $messageData) {
            $instanceName = $validated[\"instance\"][\"instanceName\"];
            $remoteJid = $messageData[\"key\"][\"remoteJid\"];
            $fromMe = $messageData[\"key\"][\"fromMe\"];
            $messageId = $messageData[\"key\"][\"id\"];
            $pushName = $messageData[\"pushName\"] ?? null;
            $messageType = $messageData[\"messageType\"];

            // Conteúdo da mensagem (pode ser 'conversation' ou 'extendedTextMessage.text')
            $content = $messageData[\"message\"][\"conversation\"] ?? $messageData[\"message\"][\"extendedTextMessage\"][\"text\"] ?? null;

            // Ignorar mensagens enviadas pelo próprio sistema (outbound) se o webhook for para inbound
            if ($fromMe) {
                continue;
            }

            // Encontrar ou criar a conversa
            $conversation = WhatsappConversation::findOrCreateByPhone(
                $remoteJid,
                $instanceName,
                $pushName
            );

            // Salvar a mensagem
            WhatsappMessage::create([
                'conversation_id' => $conversation->id,
                'message_id' => $messageId,
                'direction' => 'inbound',
                'type' => $messageType,
                'content' => $content,
                'from_phone' => $remoteJid,
                'to_phone' => $instanceName, // O número da instância que recebeu
                'status' => 'received',
                'sent_at' => now(), // Ou o timestamp do payload se disponível
            ]);

            // Broadcast evento de mensagem recebida
            $message = WhatsappMessage::where('message_id', $messageId)->first();
            if ($message) {
                broadcast(new \App\Events\MessageReceived($message));
            }

            // Atualizar a conversa
            $conversation->incrementUnread();
            $conversation->updateLastMessage();
        }

        return response()->json([\"success\" => true, \"message\" => \"Webhook de mensagem WhatsApp processado.\"]);
    }
}

