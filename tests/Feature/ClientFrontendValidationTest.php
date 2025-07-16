<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Specialty;

class ClientFrontendValidationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        
        // Criar algumas especialidades para teste
        Specialty::create(['name' => 'Cardiologia', 'cfm_code' => 'CAR']);
        Specialty::create(['name' => 'Dermatologia', 'cfm_code' => 'DER']);
    }

    /** @test */
    public function client_create_page_loads_with_specialties_select()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('Selecione uma especialidade');
        $response->assertSee('Cardiologia');
        $response->assertSee('Dermatologia');
        $response->assertSee('<select class="form-select', false);
    }

    /** @test */
    public function client_create_page_has_uf_selector()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('<select class="form-select', false);
        $response->assertSee('value="SP"', false);
        $response->assertSee('value="RJ"', false);
        $response->assertSee('value="MG"', false);
    }

    /** @test */
    public function client_create_page_has_region_selector()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('Selecione uma região');
        $response->assertSee('Norte');
        $response->assertSee('Nordeste');
        $response->assertSee('Centro-Oeste');
        $response->assertSee('Sudeste');
        $response->assertSee('Sul');
    }

    /** @test */
    public function client_create_page_has_phone_mask_attributes()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('placeholder="(11) 99999-9999"', false);
        $response->assertSee('maxlength="15"', false);
    }

    /** @test */
    public function client_create_page_has_city_validation_pattern()
    {
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('pattern="^(?!^\d+$).+"', false);
        $response->assertSee('title="A cidade não pode conter apenas números"', false);
    }

    /** @test */
    public function client_edit_page_loads_with_specialties_select()
    {
        $client = Client::create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'cpf' => '123.456.789-00',
            'specialty' => 'Cardiologia'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('clients.edit', $client));

        $response->assertStatus(200);
        $response->assertSee('Selecione uma especialidade');
        $response->assertSee('Cardiologia');
        $response->assertSee('selected', false);
    }

    /** @test */
    public function client_edit_page_has_all_frontend_validations()
    {
        $client = Client::create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'cpf' => '123.456.789-00',
            'phone' => '(11) 98765-4321',
            'state' => 'SP',
            'region' => 'Sudeste'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('clients.edit', $client));

        $response->assertStatus(200);
        
        // Verificar máscara de telefone
        $response->assertSee('placeholder="(11) 99999-9999"', false);
        $response->assertSee('maxlength="15"', false);
        
        // Verificar seletores
        $response->assertSee('<select class="form-select', false);
        $response->assertSee('value="SP" selected', false);
        $response->assertSee('value="Sudeste" selected', false);
        
        // Verificar padrão de cidade
        $response->assertSee('pattern="^(?!^\d+$).+"', false);
    }

    /** @test */
    public function phone_validation_accepts_valid_format()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'phone' => '(11) 98765-4321',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'phone' => '(11) 98765-4321'
        ]);
    }

    /** @test */
    public function phone_validation_rejects_text_only()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'phone' => 'texto apenas',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('phone');
    }

    /** @test */
    public function birth_date_validation_rejects_future_date()
    {
        $futureDate = now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'birth_date' => $futureDate,
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('birth_date');
    }

    /** @test */
    public function specialty_validation_requires_existing_specialty()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'specialty' => 'Especialidade Inexistente'
            ]);

        $response->assertSessionHasErrors('specialty');
    }

    /** @test */
    public function city_validation_rejects_numbers_only()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'service_city' => '123456',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('service_city');
    }

    /** @test */
    public function state_validation_requires_valid_uf()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'state' => 'XX',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('state');
    }

    /** @test */
    public function region_validation_requires_valid_region()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'region' => 'Região Inexistente',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('region');
    }

    /** @test */
    public function region_validation_rejects_numbers_only()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'region' => '12345',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertSessionHasErrors('region');
    }

    /** @test */
    public function can_create_client_with_all_valid_data()
    {
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'João Silva',
                'email' => 'joao@teste.com',
                'cpf' => '123.456.789-00',
                'phone' => '(11) 98765-4321',
                'birth_date' => '1990-01-15',
                'specialty' => 'Cardiologia',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
                'instagram' => '@joao'
            ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'phone' => '(11) 98765-4321',
            'specialty' => 'Cardiologia',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste'
        ]);
    }

    /** @test */
    public function can_update_client_with_all_valid_data()
    {
        $client = Client::create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'cpf' => '123.456.789-00'
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), [
                'name' => 'João Santos',
                'email' => 'joao.santos@teste.com',
                'cpf' => '123.456.789-00',
                'phone' => '(11) 98765-4321',
                'birth_date' => '1990-01-15',
                'specialty' => 'Dermatologia',
                'service_city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'region' => 'Sudeste',
                'instagram' => '@joaosantos',
                'active' => 1
            ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'João Santos',
            'email' => 'joao.santos@teste.com',
            'specialty' => 'Dermatologia',
            'state' => 'RJ'
        ]);
    }
}
