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

        // Montar payload com todos os campos necessários para o webhook
        $payload = [
            'event' => 'inscription_updated',
            'client' => $this->inscription->client->toArray(),
            'inscription' => $this->inscription->toArray(),
            'mapping' => $this->buildWebhookMapping(),
            'body' => $this->buildWebhookBody(),
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

            // Só fazer retry se não for sucesso (status 200-299)
            if (!$response->successful()) {
                Log::error('Erro ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $response->status() . ' - ' . $response->body());
                
                // Só falha se não for a última tentativa
                if ($this->attempts() < $this->tries) {
                    $this->release($this->backoff[$this->attempts() - 1] ?? 300);
                } else {
                    $this->fail(new \Exception('Webhook failed after all retries: ' . $response->status()));
                }
            } else {
                Log::info('Webhook enviado com sucesso para inscrição ' . $this->inscription->id);
            }
        } catch (\Exception $e) {
            $webhookLog->update([
                'response_status' => null,
                'response_body' => $e->getMessage(),
                'status' => 'failed',
            ]);
            
            Log::error('Exceção ao disparar webhook para inscrição ' . $this->inscription->id . ' para ' . $this->productWebhook->webhook_url . ': ' . $e->getMessage());
            
            // Só falha se não for a última tentativa
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 300);
            } else {
                $this->fail($e);
            }
        }
    }

    /**
     * Constrói o mapping do webhook conforme especificado
     */
    private function buildWebhookMapping(): array
    {
        return [
            "Nome" => "contact_name",
            "Email" => "contact_email",
            "Telefone" => "contact_phone",
            "Etapa do funil" => "deal_stage",
            "Dono do Negócio" => "deal_user",
            "Status do Negócio" => "deal_status",
            "Natureza_juridica" => "contact_natureza_juridica",
            "CPF/CNPJ" => "contact_cpfcnpj",
            "Valor_mentoria" => "contact_produto",
            "forma_pagamento_entrada" => "contact_forma_pagamento_entr",
            "Forma_pagamento_restante" => "contact_parcelas_cartao",
            "Data_pagamento" => "contact_data_contrato",
            "Rua" => "contact_endereco",
            "Numero_casa" => "contact_numero_casa",
            "Complemento" => "contact_complemento",
            "Bairro" => "contact_bairro",
            "Cidade" => "contact_cidade",
            "Estado" => "contact_estado",
            "CEP" => "contact_cep",
            "Pagamento_entrada" => "contact_pagamento_entrada",
            "Pagamento_restante" => "contact_pagamento_restante",
            "Data_pagamento_entrada" => "contact_data_pagamento_entra"
        ];
    }

    /**
     * Constrói o body do webhook com todos os dados necessários
     */
    private function buildWebhookBody(): array
    {
        $client = $this->inscription->client;
        $address = $client->addresses()->latest()->first();

        return [
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
        ];
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

