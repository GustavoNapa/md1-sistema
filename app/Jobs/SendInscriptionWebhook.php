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
        $product = $this->inscription->product;
        
        // Verificar se o produto tem webhook configurado
        if (!$product->webhook_url) {
            Log::info("Produto {$product->id} não tem webhook configurado");
            return;
        }

        // Verificar se o status da inscrição corresponde ao gatilho
        if ($this->inscription->status !== $product->webhook_trigger_status) {
            Log::info("Status da inscrição ({$this->inscription->status}) não corresponde ao gatilho ({$product->webhook_trigger_status})");
            return;
        }

        // Preparar o payload
        $payload = $this->buildPayload();

        // Criar log do webhook
        $webhookLog = WebhookLog::create([
            'inscription_id' => $this->inscription->id,
            'webhook_url' => $product->webhook_url,
            'event_type' => $this->eventType,
            'payload' => $payload,
            'attempt_number' => $this->attemptNumber,
            'status' => 'pending',
        ]);

        try {
            // Enviar webhook
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $product->webhook_token,
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'MD1-Sistema/1.0',
                ])
                ->post($product->webhook_url, $payload);

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

            Log::info("Webhook enviado com sucesso para inscrição {$this->inscription->id}");

        } catch (\Exception $e) {
            // Atualizar log com erro
            $webhookLog->update([
                'response_body' => $e->getMessage(),
                'status' => 'failed',
                'sent_at' => now(),
            ]);

            Log::error("Erro ao enviar webhook para inscrição {$this->inscription->id}: " . $e->getMessage());

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
        
        return [
            'timestamp' => now()->toISOString(),
            'event_type' => $this->eventType,
            'status' => $this->inscription->status,
            'cliente' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'cpf' => $client->cpf,
                'phone' => $client->phone,
                'birth_date' => $client->birth_date?->format('Y-m-d'),
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'zip_code' => $client->zip_code,
                'created_at' => $client->created_at?->toISOString(),
                'updated_at' => $client->updated_at?->toISOString(),
            ],
            'inscricao' => [
                'id' => $this->inscription->id,
                'status' => $this->inscription->status,
                'start_date' => $this->inscription->start_date?->format('Y-m-d'),
                'original_end_date' => $this->inscription->original_end_date?->format('Y-m-d'),
                'actual_end_date' => $this->inscription->actual_end_date?->format('Y-m-d'),
                'platform_release_date' => $this->inscription->platform_release_date?->format('Y-m-d'),
                'class_group' => $this->inscription->class_group,
                'classification' => $this->inscription->classification,
                'crmb_number' => $this->inscription->crmb_number,
                'calendar_week' => $this->inscription->calendar_week,
                'current_week' => $this->inscription->current_week,
                'amount_paid' => $this->inscription->amount_paid,
                'payment_method' => $this->inscription->payment_method,
                'has_medboss' => $this->inscription->has_medboss,
                'contrato_assinado' => $this->inscription->contrato_assinado,
                'contrato_na_pasta' => $this->inscription->contrato_na_pasta,
                'contract_folder_link' => $this->inscription->contract_folder_link,
                'commercial_notes' => $this->inscription->commercial_notes,
                'general_notes' => $this->inscription->general_notes,
                'created_at' => $this->inscription->created_at?->toISOString(),
                'updated_at' => $this->inscription->updated_at?->toISOString(),
            ],
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

