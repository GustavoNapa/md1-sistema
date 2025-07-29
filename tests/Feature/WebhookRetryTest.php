<?php

namespace Tests\Feature;

use App\Jobs\ProcessInscriptionWebhook;
use App\Models\Inscription;
use App\Models\ProductWebhook;
use App\Models\WebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookRetryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_not_retry_on_successful_response()
    {
        Queue::fake();
        
        $inscription = Inscription::factory()->create();
        $webhook = ProductWebhook::factory()->create();

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        // Verificar que o webhook foi marcado como sucesso
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'status' => 'success',
            'response_status' => 200
        ]);

        // Verificar que não houve retry
        Queue::assertNotPushed(ProcessInscriptionWebhook::class);
    }

    /** @test */
    public function it_retries_on_failed_response()
    {
        Queue::fake();
        
        $inscription = Inscription::factory()->create();
        $webhook = ProductWebhook::factory()->create();

        Http::fake([
            '*' => Http::response(['error' => 'Server error'], 500)
        ]);

        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        
        try {
            $job->handle();
        } catch (\Exception $e) {
            // Esperado falhar na primeira tentativa
        }

        // Verificar que o webhook foi marcado como falha
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'status' => 'failed',
            'response_status' => 500
        ]);
    }

    /** @test */
    public function it_includes_all_required_fields_in_webhook_body()
    {
        $inscription = Inscription::factory()->create([
            'natureza_juridica' => 'pessoa fisica',
            'valor_total' => 20000.00,
            'forma_pagamento_entrada' => 'PIX',
            'valor_entrada' => 10000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'Cartão',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
        ]);
        
        $webhook = ProductWebhook::factory()->create();

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        // Verificar que o payload contém todos os campos necessários
        $webhookLog = WebhookLog::where('inscription_id', $inscription->id)->first();
        $payload = $webhookLog->payload;

        $this->assertArrayHasKey('body', $payload);
        $body = $payload['body'];

        $this->assertArrayHasKey('contact_name', $body);
        $this->assertArrayHasKey('contact_email', $body);
        $this->assertArrayHasKey('contact_phone', $body);
        $this->assertArrayHasKey('deal_stage', $body);
        $this->assertArrayHasKey('deal_user', $body);
        $this->assertArrayHasKey('deal_status', $body);
        $this->assertArrayHasKey('contact_natureza_juridica', $body);
        $this->assertArrayHasKey('contact_cpfcnpj', $body);
        $this->assertArrayHasKey('contact_produto', $body);
        $this->assertArrayHasKey('contact_forma_pagamento_entr', $body);
        $this->assertArrayHasKey('contact_parcelas_cartao', $body);
        $this->assertArrayHasKey('contact_data_contrato', $body);
        $this->assertArrayHasKey('contact_pagamento_entrada', $body);
        $this->assertArrayHasKey('contact_pagamento_restante', $body);
        $this->assertArrayHasKey('contact_data_pagamento_entra', $body);

        // Verificar valores específicos
        $this->assertEquals('Sistema MD1', $body['deal_stage']);
        $this->assertEquals(['pessoa fisica'], $body['contact_natureza_juridica']);
        $this->assertEquals('20000', $body['contact_produto']);
        $this->assertEquals('PIX', $body['contact_forma_pagamento_entr']);
        $this->assertEquals('Cartão', $body['contact_parcelas_cartao']);
        $this->assertEquals(10000, $body['contact_pagamento_entrada']);
        $this->assertEquals(10000, $body['contact_pagamento_restante']);
    }

    /** @test */
    public function it_includes_mapping_in_webhook_payload()
    {
        $inscription = Inscription::factory()->create();
        $webhook = ProductWebhook::factory()->create();

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        $webhookLog = WebhookLog::where('inscription_id', $inscription->id)->first();
        $payload = $webhookLog->payload;

        $this->assertArrayHasKey('mapping', $payload);
        $mapping = $payload['mapping'];

        $expectedMapping = [
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

        foreach ($expectedMapping as $key => $value) {
            $this->assertArrayHasKey($key, $mapping);
            $this->assertEquals($value, $mapping[$key]);
        }
    }
}

