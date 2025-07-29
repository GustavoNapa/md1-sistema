<?php

namespace App\Jobs;

use App\Models\Inscription;
use App\Models\ProductWebhook;
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
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->productWebhook->webhook_token,
                'Content-Type' => 'application/json',
            ])->post($this->productWebhook->webhook_url, $payload);

            if ($response->successful()) {
                Log::info('Webhook para inscrição ' . $this->inscription->id . ' disparado com sucesso para ' . $this->productWebhook->webhook_url);
            } else {
                Log::error('Erro ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $response->status() . ' - ' . $response->body());
                $this->fail();
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $e->getMessage());
            $this->fail($e);
        }
    }
}


