<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscriptionFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário para autenticação
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_create_inscription_with_all_required_fields()
    {
        $client = Client::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'cpf' => '12345678901'
        ]);
        
        $product = Product::factory()->create();
        $vendor = Vendor::factory()->create();

        $data = [
            'client_id' => $client->id,
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'status' => 'active',
            'natureza_juridica' => 'pessoa fisica',
            'valor_total' => 20000.00,
            'forma_pagamento_entrada' => 'PIX',
            'valor_entrada' => 10000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'Cartão',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
            'cep' => '01234-567',
            'endereco' => 'Rua das Flores',
            'numero_casa' => '123',
            'complemento' => 'Apto 45',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ];

        $response = $this->post(route('inscriptions.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('inscriptions', [
            'client_id' => $client->id,
            'natureza_juridica' => 'pessoa fisica',
            'valor_total' => 20000.00,
            'cpf_cnpj' => $client->cpf,
        ]);

        // Verificar se o endereço foi criado
        $this->assertDatabaseHas('addresses', [
            'client_id' => $client->id,
            'cep' => '01234-567',
            'endereco' => 'Rua das Flores',
            'numero_casa' => '123',
            'cidade' => 'São Paulo',
        ]);

        // Verificar se os pagamentos foram criados
        $this->assertDatabaseHas('payments', [
            'tipo' => 'Entrada',
            'forma_pagamento' => 'PIX',
            'valor' => 10000.00,
        ]);

        $this->assertDatabaseHas('payments', [
            'tipo' => 'Pagamento Restante',
            'forma_pagamento' => 'Cartão',
            'valor' => 10000.00,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post(route('inscriptions.store'), []);

        $response->assertSessionHasErrors([
            'client_id',
            'product_id',
            'status',
            'natureza_juridica',
            'valor_total',
            'forma_pagamento_entrada',
            'valor_entrada',
            'data_pagamento_entrada',
            'forma_pagamento_restante',
            'valor_restante',
            'data_contrato',
            'cep',
            'endereco',
            'numero_casa',
            'bairro',
            'cidade',
            'estado',
        ]);
    }

    /** @test */
    public function it_validates_natureza_juridica_options()
    {
        $client = Client::factory()->create();
        $product = Product::factory()->create();

        $data = [
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'natureza_juridica' => 'invalid_option',
            'valor_total' => 20000.00,
            'forma_pagamento_entrada' => 'PIX',
            'valor_entrada' => 10000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'Cartão',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
            'cep' => '01234-567',
            'endereco' => 'Rua das Flores',
            'numero_casa' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ];

        $response = $this->post(route('inscriptions.store'), $data);

        $response->assertSessionHasErrors(['natureza_juridica']);
    }

    /** @test */
    public function it_validates_payment_method_options()
    {
        $client = Client::factory()->create();
        $product = Product::factory()->create();

        $data = [
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'natureza_juridica' => 'pessoa fisica',
            'valor_total' => 20000.00,
            'forma_pagamento_entrada' => 'INVALID',
            'valor_entrada' => 10000.00,
            'data_pagamento_entrada' => '2025-08-01',
            'forma_pagamento_restante' => 'INVALID',
            'valor_restante' => 10000.00,
            'data_contrato' => '2025-08-15',
            'cep' => '01234-567',
            'endereco' => 'Rua das Flores',
            'numero_casa' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ];

        $response = $this->post(route('inscriptions.store'), $data);

        $response->assertSessionHasErrors([
            'forma_pagamento_entrada',
            'forma_pagamento_restante'
        ]);
    }
}

