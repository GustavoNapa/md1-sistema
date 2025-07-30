<?php

namespace App\Jobs;

use App\Models\Inscription;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendInscriptionWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [300, 600, 1200]; // 5min, 10min, 20min

    protected $inscription;
    protected $eventType;
    protected $attemptNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(Inscription $inscription, string $eventType = 'inscricao.updated', int $attemptNumber = 1)
    {
        $this->inscription = $inscription;
        $this->eventType = $eventType;
        $this->attemptNumber = $attemptNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Carregar relacionamentos necessários
        $this->inscription->load(['client.addresses', 'product.webhooks']);
        
        $product = $this->inscription->product;
        
        // Buscar webhooks que correspondem ao status atual da inscrição
        $webhooks = $product->webhooks()
            ->where('webhook_trigger_status', $this->inscription->status)
            ->get();

        if ($webhooks->isEmpty()) {
            Log::info("Produto {$product->id} não tem webhooks configurados para o status '{$this->inscription->status}'");
            return;
        }

        // Preparar o payload uma vez
        $payload = $this->buildPayload();

        // Enviar para cada webhook configurado
        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook, $payload);
        }
    }

    /**
     * Send webhook to a specific URL
     */
    private function sendWebhook($webhook, $payload): void
    {
        // Criar log do webhook
        $webhookLog = WebhookLog::create([
            'inscription_id' => $this->inscription->id,
            'webhook_url' => $webhook->webhook_url,
            'event_type' => $this->eventType,
            'payload' => $payload,
            'attempt_number' => $this->attemptNumber,
            'status' => 'pending',
        ]);

        try {
            // Enviar webhook
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $webhook->webhook_token,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'MD1-Sistema/1.0',
                ])
                ->post($webhook->webhook_url, $payload);

            // Atualizar log com resposta
            $webhookLog->update([
                'response_status' => $response->status(),
                'response_body' => $response->body(),
                'status' => $response->successful() ? 'success' : 'failed',
                'sent_at' => now(),
            ]);

            if (!$response->successful()) {
                throw new \Exception("Webhook failed with status {$response->status()}: {$response->body()}");
            }

            Log::info("Webhook enviado com sucesso para inscrição {$this->inscription->id} - URL: {$webhook->webhook_url}");

        } catch (\Exception $e) {
            // Atualizar log com erro
            $webhookLog->update([
                'response_body' => $e->getMessage(),
                'status' => 'failed',
                'sent_at' => now(),
            ]);

            Log::error("Erro ao enviar webhook para inscrição {$this->inscription->id} - URL: {$webhook->webhook_url} - Erro: " . $e->getMessage());

            // Re-lançar exceção para que o Laravel tente novamente
            throw $e;
        }
    }

    /**
     * Build the webhook payload
     */
    private function buildPayload(): array
    {
        $client = $this->inscription->client;
        $address = $client->addresses()->latest()->first();
        
        // Log para debug se não houver endereço
        if (!$address) {
            Log::warning('Nenhum endereço encontrado para o cliente no SendInscriptionWebhook', [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'inscription_id' => $this->inscription->id
            ]);
        }
        
        return [
            'timestamp' => now()->toISOString(),
            'event_type' => $this->eventType,
            'status' => $this->inscription->status,
            'event' => 'inscription_updated',
            'client' => $client->toArray(),
            'inscription' => $this->inscription->toArray(),
            'mapping' => [
                'Nome' => 'contact_name',
                'Email' => 'contact_email',
                'Telefone' => 'contact_phone',
                'Etapa do funil' => 'deal_stage',
                'Dono do Negócio' => 'deal_user',
                'Status do Negócio' => 'deal_status',
                'Natureza_juridica' => 'contact_natureza_juridica',
                'CPF/CNPJ' => 'contact_cpfcnpj',
                'Valor_mentoria' => 'contact_produto',
                'forma_pagamento_entrada' => 'contact_forma_pagamento_entr',
                'Forma_pagamento_restante' => 'contact_parcelas_cartao',
                'Data_pagamento' => 'contact_data_contrato',
                'Rua' => 'contact_endereco',
                'Numero_casa' => 'contact_numero_casa',
                'Complemento' => 'contact_complemento',
                'Bairro' => 'contact_bairro',
                'Cidade' => 'contact_cidade',
                'Estado' => 'contact_estado',
                'CEP' => 'contact_cep',
                'Pagamento_entrada' => 'contact_pagamento_entrada',
                'Pagamento_restante' => 'contact_pagamento_restante',
                'Data_pagamento_entrada' => 'contact_data_pagamento_entra',
            ],
            'body' => [
                "contact_name" => $client->name,
                "contact_email" => $client->email,
                "contact_phone" => $client->phone,
                "deal_stage" => "Sistema MD1",
                "deal_user" => auth()->user()->email ?? 'sistema@md1.com',
                "deal_status" => strtoupper($this->inscription->status),
                "contact_natureza_juridica" => [$this->inscription->natureza_juridica],
                "contact_cpfcnpj" => $this->inscription->cpf_cnpj,
                "contact_produto" => number_format($this->inscription->valor_total, 0, '', ''),
                "contact_forma_pagamento_entr" => $this->inscription->forma_pagamento_entrada,
                "contact_parcelas_cartao" => $this->inscription->forma_pagamento_restante,
                "contact_data_contrato" => $this->inscription->data_contrato?->format('d/m/Y'),
                "contact_endereco" => $address?->endereco,
                "contact_numero_casa" => $address?->numero_casa,
                "contact_complemento" => $address?->complemento,
                "contact_bairro" => $address?->bairro,
                "contact_cidade" => $address?->cidade,
                "contact_estado" => $address?->estado,
                "contact_cep" => $address?->cep,
                "contact_pagamento_entrada" => (int)$this->inscription->valor_entrada,
                "contact_pagamento_restante" => (int)$this->inscription->valor_restante,
                "contact_data_pagamento_entra" => $this->inscription->data_pagamento_entrada?->format('d/m/Y')
            ]
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Webhook job failed definitivamente para inscrição {$this->inscription->id}: " . $exception->getMessage());
    }
}

