<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;

class ClientFormValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create and authenticate a user for all tests
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $this->actingAs($this->user);
    }

    /**
     * Test phone number validation - should require proper format
     */
    public function test_phone_number_validation()
    {
        // Test invalid phone formats
        $invalidPhones = [
            'abc1234567',        // letters in phone
            '123',               // too short
            '123456789012345',   // too long
            'abcdefghij',        // only letters
            '11 9999-9999',      // missing digit
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->post('/clients', [
                'name' => 'João Silva',
                'cpf' => '12345678901',
                'email' => 'joao@example.com',
                'phone' => $phone,
                'birth_date' => '1990-01-01',
                'specialty' => 'Cardiologia',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionHasErrors('phone');
        }

        // Test valid phone formats
        $validPhones = [
            '11999999999',       // 11 digits
            '(11) 99999-9999',   // formatted
            '11 99999-9999',     // with space
        ];

        foreach ($validPhones as $phone) {
            $response = $this->post('/clients', [
                'name' => 'João Silva ' . rand(1000, 9999), // unique name
                'cpf' => '1234567890' . rand(1, 9),         // unique cpf
                'email' => 'joao' . rand(1000, 9999) . '@example.com', // unique email
                'phone' => $phone,
                'birth_date' => '1990-01-01',
                'specialty' => 'Cardiologia',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionDoesntHaveErrors('phone');
        }
    }

    /**
     * Test birth date validation - cannot be in the future
     */
    public function test_birth_date_cannot_be_future()
    {
        $futureDate = Carbon::now()->addDay()->format('Y-m-d');
        
        $response = $this->post('/clients', [
            'name' => 'Maria Silva',
            'cpf' => '98765432100',
            'email' => 'maria@example.com',
            'phone' => '21999999999',
            'birth_date' => $futureDate,
            'specialty' => 'Pediatria',
            'service_city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'region' => 'Sudeste',
        ]);

        $response->assertSessionHasErrors('birth_date');
    }

    /**
     * Test birth date validation - valid past dates should work
     */
    public function test_birth_date_past_dates_valid()
    {
        $validDates = [
            '1990-01-01',
            '1985-12-31',
            Carbon::now()->subYears(25)->format('Y-m-d'),
            Carbon::now()->subDay()->format('Y-m-d'), // yesterday
        ];

        foreach ($validDates as $date) {
            $response = $this->post('/clients', [
                'name' => 'Cliente ' . rand(1000, 9999),
                'cpf' => '1111111111' . rand(0, 9),
                'email' => 'cliente' . rand(1000, 9999) . '@example.com',
                'phone' => '11999999999',
                'birth_date' => $date,
                'specialty' => 'Medicina Geral',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionDoesntHaveErrors('birth_date');
        }
    }

    /**
     * Test specialty validation - should be from predefined list
     */
    public function test_specialty_validation()
    {
        // Test invalid specialty
        $response = $this->post('/clients', [
            'name' => 'Dr. Pedro',
            'cpf' => '11122233344',
            'email' => 'pedro@example.com',
            'phone' => '11977777777',
            'birth_date' => '1980-01-01',
            'specialty' => 'Especialidade Inexistente',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste',
        ]);

        $response->assertSessionHasErrors('specialty');

        // Test valid specialties from CFM
        $validSpecialties = [
            'Cardiologia',
            'Pediatria',
            'Ginecologia e Obstetrícia',
            'Medicina Interna',
            'Cirurgia Geral',
        ];

        foreach ($validSpecialties as $specialty) {
            $response = $this->post('/clients', [
                'name' => 'Dr. ' . rand(1000, 9999),
                'cpf' => '2222222222' . rand(0, 9),
                'email' => 'dr' . rand(1000, 9999) . '@example.com',
                'phone' => '11999999999',
                'birth_date' => '1980-01-01',
                'specialty' => $specialty,
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionDoesntHaveErrors('specialty');
        }
    }

    /**
     * Test city validation - cannot be only numbers
     */
    public function test_city_validation()
    {
        // Invalid cities (only numbers)
        $invalidCities = [
            '123456',
            '999',
            '12345678',
        ];

        foreach ($invalidCities as $city) {
            $response = $this->post('/clients', [
                'name' => 'Ana Silva',
                'cpf' => '33344455566',
                'email' => 'ana@example.com',
                'phone' => '11888888888',
                'birth_date' => '1985-01-01',
                'specialty' => 'Dermatologia',
                'service_city' => $city,
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionHasErrors('service_city');
        }

        // Valid cities
        $validCities = [
            'São Paulo',
            'Rio de Janeiro',
            'Belo Horizonte',
            'Salvador',
            'Brasília',
        ];

        foreach ($validCities as $city) {
            $response = $this->post('/clients', [
                'name' => 'Cliente ' . rand(1000, 9999),
                'cpf' => '4444444444' . rand(0, 9),
                'email' => 'cliente' . rand(1000, 9999) . '@example.com',
                'phone' => '11999999999',
                'birth_date' => '1985-01-01',
                'specialty' => 'Dermatologia',
                'service_city' => $city,
                'state' => 'SP',
                'region' => 'Sudeste',
            ]);

            $response->assertSessionDoesntHaveErrors('service_city');
        }
    }

    /**
     * Test state validation - should be valid UF
     */
    public function test_state_validation()
    {
        // Invalid states
        $invalidStates = [
            'XX',
            'ZZ',
            'ABC',
            'São Paulo', // full name instead of UF
            '12',
        ];

        foreach ($invalidStates as $state) {
            $response = $this->post('/clients', [
                'name' => 'Carlos Silva',
                'cpf' => '55566677788',
                'email' => 'carlos@example.com',
                'phone' => '11777777777',
                'birth_date' => '1975-01-01',
                'specialty' => 'Ortopedia',
                'service_city' => 'São Paulo',
                'state' => $state,
                'region' => 'Sudeste',
            ]);

            $response->assertSessionHasErrors('state');
        }

        // Valid states (UF)
        $validStates = ['SP', 'RJ', 'MG', 'RS', 'PR', 'SC', 'BA', 'GO', 'DF'];

        foreach ($validStates as $state) {
            $response = $this->post('/clients', [
                'name' => 'Cliente ' . rand(1000, 9999),
                'cpf' => '6666666666' . rand(0, 9),
                'email' => 'cliente' . rand(1000, 9999) . '@example.com',
                'phone' => '11999999999',
                'birth_date' => '1975-01-01',
                'specialty' => 'Ortopedia',
                'service_city' => 'São Paulo',
                'state' => $state,
                'region' => 'Sudeste',
            ]);

            $response->assertSessionDoesntHaveErrors('state');
        }
    }

    /**
     * Test region validation - cannot be only numbers
     */
    public function test_region_validation()
    {
        // Invalid regions (only numbers)
        $invalidRegions = [
            '123',
            '999',
            '12345',
        ];

        foreach ($invalidRegions as $region) {
            $response = $this->post('/clients', [
                'name' => 'Roberto Silva',
                'cpf' => '77788899900',
                'email' => 'roberto@example.com',
                'phone' => '11666666666',
                'birth_date' => '1970-01-01',
                'specialty' => 'Neurologia',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => $region,
            ]);

            $response->assertSessionHasErrors('region');
        }

        // Valid regions
        $validRegions = [
            'Sudeste',
            'Sul',
            'Nordeste',
            'Norte',
            'Centro-Oeste',
        ];

        foreach ($validRegions as $region) {
            $response = $this->post('/clients', [
                'name' => 'Cliente ' . rand(1000, 9999),
                'cpf' => '8888888888' . rand(0, 9),
                'email' => 'cliente' . rand(1000, 9999) . '@example.com',
                'phone' => '11999999999',
                'birth_date' => '1970-01-01',
                'specialty' => 'Neurologia',
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => $region,
            ]);

            $response->assertSessionDoesntHaveErrors('region');
        }
    }

    /**
     * Test edit form validation
     */
    public function test_edit_form_validation()
    {
        // Create a client first
        $client = Client::create([
            'name' => 'Cliente Original',
            'cpf' => '99999999999',
            'email' => 'original@example.com',
            'phone' => '11999999999',
            'birth_date' => '1980-01-01',
            'specialty' => 'Cardiologia',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste',
            'active' => true,
        ]);

        // Test invalid phone on edit
        $response = $this->put("/clients/{$client->id}", [
            'name' => 'Cliente Editado',
            'cpf' => '99999999999',
            'email' => 'editado@example.com',
            'phone' => 'telefone_inválido',
            'birth_date' => '1980-01-01',
            'specialty' => 'Cardiologia',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste',
            'active' => 1,
        ]);

        $response->assertSessionHasErrors('phone');

        // Test valid edit
        $response = $this->put("/clients/{$client->id}", [
            'name' => 'Cliente Editado',
            'cpf' => '99999999999',
            'email' => 'editado@example.com',
            'phone' => '11888888888',
            'birth_date' => '1980-01-01',
            'specialty' => 'Pediatria',
            'service_city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'region' => 'Sudeste',
            'active' => 1,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect("/clients");
    }
}
