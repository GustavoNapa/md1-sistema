<?php

namespace App\Jobs;

use App\Models\Inscription;
use App\Models\WebhookLog;
use App\Models\PaymentPlatform;
use App\Models\PaymentChannel;
use Carbon\Carbon;
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

        // Resolve plataformas / canais legíveis com fallback
        $resolvePlatform = function ($val) {
            if (!$val) return null;
            // se id numérico, buscar PaymentPlatform
            if (is_numeric($val)) {
                return PaymentPlatform::find($val)?->toArray();
            }
            // tentar achar por id string numérica
            if (ctype_digit((string)$val)) {
                return PaymentPlatform::find((int)$val)?->toArray();
            }
            // buscar por name
            return PaymentPlatform::where('name', $val)->first()?->toArray();
        };

        $resolveChannel = function ($val) {
            if (!$val) return null;
            if (is_numeric($val)) {
                return PaymentChannel::find($val)?->toArray();
            }
            if (ctype_digit((string)$val)) {
                return PaymentChannel::find((int)$val)?->toArray();
            }
            return PaymentChannel::where('name', $val)->first()?->toArray();
        };

        // Tenta resolver forma (payment channel method) por payment_channel_id + installments/name
        $resolveMethod = function ($channelId, $formaVal) {
            if (!$channelId || $formaVal === null || $formaVal === '') return null;
            // if numeric -> search installments
            if (is_numeric($formaVal)) {
                $m = \DB::table('payment_channel_methods')
                    ->where('payment_channel_id', $channelId)
                    ->where('installments', (int)$formaVal)
                    ->first();
                if ($m) return (array)$m;
            }
            // try match by name
            $m = \DB::table('payment_channel_methods')
                ->where('payment_channel_id', $channelId)
                ->where('name', $formaVal)
                ->first();
            if ($m) return (array)$m;

            return null;
        };

        // identificar plataformas/canais/formas para avista/entrada/restante
        $pl_avista = $resolvePlatform($this->inscription->meio_pagamento_avista ?? $this->inscription->payment_means ?? null);
        $ch_avista = $resolveChannel($this->inscription->payment_channel_avista ?? $this->inscription->payment_location ?? null);
        $method_avista = $resolveMethod($ch_avista['id'] ?? null, $this->inscription->forma_pagamento_avista ?? null);

        $pl_entrada = $resolvePlatform($this->inscription->meio_pagamento_entrada ?? null);
        $ch_entrada = $resolveChannel($this->inscription->payment_channel_entrada ?? null);
        $method_entrada = $resolveMethod($ch_entrada['id'] ?? null, $this->inscription->forma_pagamento_entrada ?? null);

        $pl_rest = $resolvePlatform($this->inscription->meio_pagamento_restante ?? null);
        $ch_rest = $resolveChannel($this->inscription->payment_channel_restante ?? null);
        $method_rest = $resolveMethod($ch_rest['id'] ?? null, $this->inscription->forma_pagamento_restante ?? null);

        // Garantir contact_phone não seja nulo: tenta client->phone, fallback para última phone relation, email ou ''
        $contactPhone = null;
        if (!empty($client->phone)) {
            $contactPhone = (string) $client->phone;
        } else {
            // Se o model Client tiver relação phones, tentar buscar o último número
            if (method_exists($client, 'phones')) {
                try {
                    $latestPhone = $client->phones()->whereNotNull('number')->orderBy('created_at', 'desc')->value('number');
                    if ($latestPhone) {
                        $contactPhone = (string) $latestPhone;
                    }
                } catch (\Throwable $e) {
                    // não falhar aqui; apenas logar
                    Log::debug('Erro ao recuperar phones relation do cliente no webhook: ' . $e->getMessage());
                }
            }
        }
        // fallback final: email ou string vazia (nunca null)
        if (empty($contactPhone)) {
            if (!empty($client->email)) {
                $contactPhone = (string) $client->email;
                Log::warning("Nenhum telefone encontrado para client_id {$client->id}; usando email como fallback no webhook payload.");
            } else {
                $contactPhone = '';
                Log::warning("Nenhum telefone ou email encontrado para client_id {$client->id}; usando string vazia para contact_phone no webhook payload.");
            }
        }

        // Log de aviso se endereço não existir
        if (!$address) {
            Log::warning('Nenhum endereço encontrado para o cliente no SendInscriptionWebhook', [
                'client_id' => $client->id ?? null,
                'inscription_id' => $this->inscription->id
            ]);
        }

        return [
            'timestamp' => now()->toIso8601String(),
            'event_type' => $this->eventType,
            'status' => $this->inscription->status,
            'event' => $this->eventType === 'inscricao.created' ? 'inscription_created' : 'inscription_updated',
            'client' => $client?->toArray(),
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
                'PaymentPlatform_avista' => 'contact_payment_platform_avista',
                'PaymentChannel_avista' => 'contact_payment_channel_avista',
                'PaymentMethod_avista' => 'contact_payment_method_avista',
            ],
            'body' => [
                "contact_name" => $client->name,
                "contact_email" => $client->email,
                "contact_phone" => $contactPhone, // nunca null
                "deal_stage" => "Sistema MD1",
                "deal_user" => auth()->user()->email ?? 'sistema@md1.com',
                "deal_status" => strtoupper($this->inscription->status),
                "contact_natureza_juridica" => [$this->inscription->natureza_juridica],
                "contact_cpfcnpj" => $this->inscription->cpf_cnpj,
                "contact_produto" => number_format($this->inscription->valor_total ?? 0, 0, '', ''),
                // formas antigas (podem ser id ou installments)
                "contact_forma_pagamento_entr" => $this->inscription->forma_pagamento_entrada ?? null,
                "contact_parcelas_cartao" => $this->inscription->forma_pagamento_restante ?? null,
                "contact_data_contrato" => $this->inscription->data_contrato?->format('d/m/Y') ?? null,
                "contact_endereco" => $address?->endereco,
                "contact_numero_casa" => $address?->numero_casa,
                "contact_complemento" => $address?->complemento,
                "contact_bairro" => $address?->bairro,
                "contact_cidade" => $address?->cidade,
                "contact_estado" => $address?->estado,
                "contact_cep" => $address?->cep,
                "contact_pagamento_entrada" => (float) ($this->inscription->valor_entrada ?? 0),
                "contact_pagamento_restante" => (float) ($this->inscription->valor_restante ?? 0),
                "contact_data_pagamento_entra" => $this->inscription->data_pagamento_entrada?->format('d/m/Y') ?? Carbon::now()->format('d/m/Y'),
                // Novos campos com objetos legíveis
                "contact_payment_platform_avista" => $pl_avista,
                "contact_payment_channel_avista" => $ch_avista,
                "contact_payment_method_avista" => $method_avista,
                "contact_payment_platform_entrada" => $pl_entrada,
                "contact_payment_channel_entrada" => $ch_entrada,
                "contact_payment_method_entrada" => $method_entrada,
                "contact_payment_platform_restante" => $pl_rest,
                "contact_payment_channel_restante" => $ch_rest,
                "contact_payment_method_restante" => $method_rest,
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

