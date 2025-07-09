<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Inscription;
use App\Models\Document;
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
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Log da requisição
            Log::info('Webhook recebido', [
                'action' => $action,
                'data' => $request->all(),
                'ip' => $request->ip()
            ]);

            // Processar ação específica
            switch ($action) {
                case 'attach-document':
                    return $this->attachDocument($request);
                
                case 'update-client':
                    return $this->updateClient($request);
                
                case 'update-inscription':
                    return $this->updateInscription($request);
                
                case 'send-whatsapp':
                    return $this->sendWhatsappMessage($request);
                
                default:
                    return response()->json(['error' => 'Action not supported'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Anexar documento a uma inscrição
     */
    private function attachDocument(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:100',
            'document_url' => 'required|url',
            'file_size' => 'nullable|integer'
        ]);

        $inscription = Inscription::findOrFail($validated['inscription_id']);

        // Baixar arquivo da URL
        $fileContent = file_get_contents($validated['document_url']);
        $fileName = time() . '_' . $validated['document_name'];
        $filePath = "documents/{$inscription->id}/{$fileName}";

        Storage::put($filePath, $fileContent);

        // Criar registro do documento
        $document = Document::create([
            'inscription_id' => $inscription->id,
            'name' => $validated['document_name'],
            'type' => $validated['document_type'],
            'path' => $filePath,
            'size' => $validated['file_size'] ?? strlen($fileContent),
            'upload_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento anexado com sucesso',
            'document_id' => $document->id
        ]);
    }

    /**
     * Atualizar dados do cliente
     */
    private function updateClient(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'data' => 'required|array'
        ]);

        $client = Client::findOrFail($validated['client_id']);
        
        // Filtrar apenas campos permitidos
        $allowedFields = ['name', 'email', 'phone', 'specialty', 'service_city', 'state', 'region', 'instagram'];
        $updateData = array_intersect_key($validated['data'], array_flip($allowedFields));

        $client->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Cliente atualizado com sucesso',
            'client' => $client->fresh()
        ]);
    }

    /**
     * Atualizar dados da inscrição
     */
    private function updateInscription(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'data' => 'required|array'
        ]);

        $inscription = Inscription::findOrFail($validated['inscription_id']);
        
        // Filtrar apenas campos permitidos
        $allowedFields = [
            'status', 'classification', 'current_week', 'calendar_week',
            'commercial_notes', 'general_notes', 'actual_end_date'
        ];
        $updateData = array_intersect_key($validated['data'], array_flip($allowedFields));

        $inscription->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Inscrição atualizada com sucesso',
            'inscription' => $inscription->fresh()
        ]);
    }

    /**
     * Enviar mensagem WhatsApp (placeholder)
     */
    private function sendWhatsappMessage(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'message' => 'required|string',
            'type' => 'nullable|string|in:text,image,document'
        ]);

        // TODO: Implementar integração com Evolution API
        Log::info('Mensagem WhatsApp solicitada', $validated);

        return response()->json([
            'success' => true,
            'message' => 'Mensagem WhatsApp processada',
            'status' => 'queued'
        ]);
    }

    /**
     * Validar assinatura HMAC
     */
    private function validateSignature(Request $request): bool
    {
        $secret = config('app.webhook_secret');
        
        if (!$secret) {
            return true; // Se não há secret configurado, pular validação
        }

        $signature = $request->header('X-MD1-Signature');
        
        if (!$signature) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Endpoint de teste para webhooks
     */
    public function test(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint funcionando',
            'timestamp' => now()->toISOString(),
            'received_data' => $request->all()
        ]);
    }
}
