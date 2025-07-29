<?php

namespace Tests\Feature;

use App\Jobs\ProcessInscriptionWebhook;
use App\Models\Address;
use App\Models\Client;
use App\Models\Inscription;
use App\Models\ProductWebhook;
use App\Models\WebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_includes_address_fields_in_webhook_body()
    {
        // Criar cliente
        $client = Client::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'cpf' => '12345678901'
        ]);

        // Criar endereço para o cliente
        $address = Address::create([
            'client_id' => $client->id,
            'cep' => '01234-567',
            'endereco' => 'Rua das Flores',
            'numero_casa' => '123',
            'complemento' => 'Apto 45',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        // Criar inscrição
        $inscription = Inscription::factory()->create([
            'client_id' => $client->id,
            'natureza_juridica' => 'pessoa fisica',
            'cpf_cnpj' => $client->cpf,
            'valor_total' => 20000.00,
            'forma_pagamento_entrada' => 'PIX',
            'valor_entrada' => 10000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'Cartão',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
        ]);

        // Criar webhook
        $webhook = ProductWebhook::factory()->create();

        // Mock da resposta HTTP
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        // Executar o job do webhook
        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        // Verificar que o webhook foi executado com sucesso
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'status' => 'success',
            'response_status' => 200
        ]);

        // Recuperar o log do webhook para verificar o payload
        $webhookLog = WebhookLog::where('inscription_id', $inscription->id)->first();
        $payload = $webhookLog->payload;

        // Verificar que o body contém os campos de endereço
        $this->assertArrayHasKey('body', $payload);
        $body = $payload['body'];

        // Verificar campos de endereço específicos
        $this->assertArrayHasKey('contact_endereco', $body);
        $this->assertArrayHasKey('contact_numero_casa', $body);
        $this->assertArrayHasKey('contact_complemento', $body);
        $this->assertArrayHasKey('contact_bairro', $body);
        $this->assertArrayHasKey('contact_cidade', $body);
        $this->assertArrayHasKey('contact_estado', $body);
        $this->assertArrayHasKey('contact_cep', $body);

        // Verificar que os valores não são nulos
        $this->assertEquals('Rua das Flores', $body['contact_endereco']);
        $this->assertEquals('123', $body['contact_numero_casa']);
        $this->assertEquals('Apto 45', $body['contact_complemento']);
        $this->assertEquals('Centro', $body['contact_bairro']);
        $this->assertEquals('São Paulo', $body['contact_cidade']);
        $this->assertEquals('SP', $body['contact_estado']);
        $this->assertEquals('01234-567', $body['contact_cep']);
    }

    /** @test */
    public function it_handles_missing_address_gracefully()
    {
        // Criar cliente sem endereço
        $client = Client::factory()->create([
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone' => '11888888888',
            'cpf' => '98765432100'
        ]);

        // Criar inscrição
        $inscription = Inscription::factory()->create([
            'client_id' => $client->id,
            'natureza_juridica' => 'pessoa juridica',
            'cpf_cnpj' => $client->cpf,
            'valor_total' => 15000.00,
            'forma_pagamento_entrada' => 'Boleto',
            'valor_entrada' => 5000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'PIX',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
        ]);

        // Criar webhook
        $webhook = ProductWebhook::factory()->create();

        // Mock da resposta HTTP
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        // Executar o job do webhook
        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        // Verificar que o webhook foi executado com sucesso mesmo sem endereço
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'status' => 'success',
            'response_status' => 200
        ]);

        // Recuperar o log do webhook para verificar o payload
        $webhookLog = WebhookLog::where('inscription_id', $inscription->id)->first();
        $payload = $webhookLog->payload;

        // Verificar que o body contém os campos de endereço como null
        $body = $payload['body'];
        $this->assertNull($body['contact_endereco']);
        $this->assertNull($body['contact_numero_casa']);
        $this->assertNull($body['contact_complemento']);
        $this->assertNull($body['contact_bairro']);
        $this->assertNull($body['contact_cidade']);
        $this->assertNull($body['contact_estado']);
        $this->assertNull($body['contact_cep']);

        // Verificar que outros campos ainda funcionam
        $this->assertEquals('Maria Silva', $body['contact_name']);
        $this->assertEquals('maria@example.com', $body['contact_email']);
        $this->assertEquals(['pessoa juridica'], $body['contact_natureza_juridica']);
    }

    /** @test */
    public function it_uses_latest_address_when_multiple_exist()
    {
        // Criar cliente
        $client = Client::factory()->create();

        // Criar múltiplos endereços
        $oldAddress = Address::create([
            'client_id' => $client->id,
            'cep' => '00000-000',
            'endereco' => 'Endereço Antigo',
            'numero_casa' => '999',
            'bairro' => 'Bairro Antigo',
            'cidade' => 'Cidade Antiga',
            'estado' => 'XX',
            'created_at' => now()->subDays(10),
        ]);

        sleep(1); // Garantir diferença de timestamp

        $newAddress = Address::create([
            'client_id' => $client->id,
            'cep' => '11111-111',
            'endereco' => 'Endereço Novo',
            'numero_casa' => '111',
            'bairro' => 'Bairro Novo',
            'cidade' => 'Cidade Nova',
            'estado' => 'YY',
        ]);

        // Criar inscrição
        $inscription = Inscription::factory()->create([
            'client_id' => $client->id,
        ]);

        // Criar webhook
        $webhook = ProductWebhook::factory()->create();

        // Mock da resposta HTTP
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        // Executar o job do webhook
        $job = new ProcessInscriptionWebhook($inscription, $webhook);
        $job->handle();

        // Recuperar o log do webhook
        $webhookLog = WebhookLog::where('inscription_id', $inscription->id)->first();
        $body = $webhookLog->payload['body'];

        // Verificar que usa o endereço mais recente
        $this->assertEquals('Endereço Novo', $body['contact_endereco']);
        $this->assertEquals('111', $body['contact_numero_casa']);
        $this->assertEquals('Bairro Novo', $body['contact_bairro']);
        $this->assertEquals('Cidade Nova', $body['contact_cidade']);
        $this->assertEquals('YY', $body['contact_estado']);
        $this->assertEquals('11111-111', $body['contact_cep']);
    }
}

