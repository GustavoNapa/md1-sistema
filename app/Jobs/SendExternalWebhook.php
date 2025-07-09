<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendExternalWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $data;
    protected $event;
    protected $secret;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, array $data, string $event, string $secret = null)
    {
        $this->url = $url;
        $this->data = $data;
        $this->event = $event;
        $this->secret = $secret ?? config('app.webhook_secret', 'md1-webhook-secret');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payload = [
                'event' => $this->event,
                'data' => $this->data,
                'timestamp' => now()->toISOString(),
                'source' => 'md1-clients-system'
            ];

            $headers = [
                'Content-Type' => 'application/json',
                'User-Agent' => 'MD1-Clients-Webhook/1.0',
            ];

            // Adicionar assinatura HMAC se secret estiver configurado
            if ($this->secret) {
                $signature = hash_hmac('sha256', json_encode($payload), $this->secret);
                $headers['X-MD1-Signature'] = 'sha256=' . $signature;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->retry(3, 1000)
                ->post($this->url, $payload);

            if ($response->successful()) {
                Log::info('Webhook enviado com sucesso', [
                    'url' => $this->url,
                    'event' => $this->event,
                    'status' => $response->status()
                ]);
            } else {
                Log::error('Falha ao enviar webhook', [
                    'url' => $this->url,
                    'event' => $this->event,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                throw new \Exception('Webhook failed with status: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'url' => $this->url,
                'event' => $this->event,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de webhook falhou definitivamente', [
            'url' => $this->url,
            'event' => $this->event,
            'error' => $exception->getMessage()
        ]);
    }
}
