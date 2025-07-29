<?php

namespace App\Jobs;

use App\Models\WhatsappMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [10, 30, 60]; // Retry após 10s, 30s, 60s

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(WhatsappMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando envio de mensagem WhatsApp', [
                'message_id' => $this->message->id,
                'to_phone' => $this->message->to_phone,
                'attempt' => $this->attempts()
            ]);

            // Verificar se a mensagem ainda está pendente
            if ($this->message->status !== 'pending') {
                Log::info('Mensagem não está mais pendente, cancelando envio', [
                    'message_id' => $this->message->id,
                    'status' => $this->message->status
                ]);
                return;
            }

            // Marcar como enviando
            $this->message->update(['status' => 'sending']);

            // Enviar via Evolution API
            $success = $this->sendToEvolutionAPI();

            if ($success) {
                $this->message->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                // Broadcast evento de mensagem enviada
                broadcast(new \App\Events\MessageSent($this->message));

                Log::info('Mensagem WhatsApp enviada com sucesso', [
                    'message_id' => $this->message->id,
                    'to_phone' => $this->message->to_phone
                ]);
            } else {
                throw new \Exception('Falha ao enviar mensagem via Evolution API');
            }

        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem WhatsApp', [
                'message_id' => $this->message->id,
                'to_phone' => $this->message->to_phone,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Se esgotaram as tentativas, marcar como falha
            if ($this->attempts() >= $this->tries) {
                $this->message->update(['status' => 'failed']);
                Log::error('Mensagem WhatsApp falhou após todas as tentativas', [
                    'message_id' => $this->message->id,
                    'to_phone' => $this->message->to_phone
                ]);
            } else {
                // Voltar para pendente para nova tentativa
                $this->message->update(['status' => 'pending']);
            }

            throw $e; // Re-throw para que o Laravel gerencie o retry
        }
    }

    /**
     * Enviar mensagem via Evolution API
     */
    private function sendToEvolutionAPI(): bool
    {
        try {
            $baseUrl = config('services.evolution.base_url');
            $apiKey = config('services.evolution.api_key');
            $instanceName = config('services.evolution.instance_name');

            $url = rtrim($baseUrl, '/') . "/message/sendText/{$instanceName}";
            
            $payload = [
                'number' => $this->message->to_phone,
                'text' => $this->message->content
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Atualizar message_id se retornado pela API
                if (isset($responseData['key']['id'])) {
                    $this->message->update(['message_id' => $responseData['key']['id']]);
                }

                Log::info('Resposta da Evolution API', [
                    'message_id' => $this->message->id,
                    'response' => $responseData
                ]);

                return true;
            } else {
                Log::error('Evolution API retornou erro', [
                    'message_id' => $this->message->id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Exceção ao chamar Evolution API', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de envio de mensagem WhatsApp falhou definitivamente', [
            'message_id' => $this->message->id,
            'to_phone' => $this->message->to_phone,
            'error' => $exception->getMessage()
        ]);

        $this->message->update(['status' => 'failed']);
    }
}

