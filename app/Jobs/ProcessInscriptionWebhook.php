<?php

namespace App\Jobs;

use App\Models\Inscription;
use App\Models\ProductWebhook;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessInscriptionWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $inscription;
    public $productWebhook;
    public $tries = 3; // Número de tentativas
    public $backoff = [10, 60, 300]; // Atraso em segundos entre as tentativas

    /**
     * Create a new job instance.
     */
    public function __construct(Inscription $inscription, ProductWebhook $productWebhook)
    {
        $this->inscription = $inscription;
        $this->productWebhook = $productWebhook;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Carregar o relacionamento 'client' para garantir que todos os dados do cliente estejam disponíveis
        $this->inscription->load("client");

        $payload = [
            'event' => 'inscription_updated',
            'client' => $this->inscription->client->toArray(),
            'inscription' => $this->inscription->toArray(),
            'mapping' => config('webhook_mapping'),
        ];

        // Encontrar ou criar um log para esta tentativa
        $webhookLog = WebhookLog::firstOrNew([
            'inscription_id' => $this->inscription->id,
            'webhook_url' => $this->productWebhook->webhook_url,
            'event_type' => 'inscription_updated',
            'attempt_number' => $this->attempts(),
        ]);

        $webhookLog->payload = $payload;
        $webhookLog->status = 'pending';
        $webhookLog->sent_at = now();
        $webhookLog->save();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->productWebhook->webhook_token,
                'Content-Type' => 'application/json',
            ])->post($this->productWebhook->webhook_url, $payload);

            $webhookLog->update([
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'status' => $response->successful() ? 'success' : 'failed',
            ]);

            if (!$response->successful()) {
                Log::error('Erro ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $response->status() . ' - ' . $response->body());
                $this->fail();
            }
        } catch (\Exception $e) {
            $webhookLog->update([
                'response_status' => null,
                'response_body' => $e->getMessage(),
                'status' => 'failed',
            ]);
            Log::error('Exceção ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::critical('ProcessInscriptionWebhook falhou após todas as tentativas.', [
            'inscription_id' => $this->inscription->id,
            'webhook_url' => $this->productWebhook->webhook_url,
            'exception' => $exception->getMessage(),
        ]);
    }
}


