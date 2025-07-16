<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Specialty;

class PhoneValidationDebugTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Specialty::create(['name' => 'Cardiologia', 'cfm_code' => 'CAR']);
    }

    /** @test */
    public function debug_phone_validation()
    {
        // Teste com telefone inválido
        $data = [
            'name' => 'Teste Debug',
            'email' => 'debug@exemplo.com',
            'cpf' => '123.456.789-00',
            'phone' => 'abc123def',
            'specialty' => 'Cardiologia'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), $data);

        dd([
            'status' => $response->getStatusCode(),
            'session_errors' => session('errors'),
            'response_headers' => $response->headers->all(),
            'has_errors' => $response->getSession()->hasErrors(),
            'errors' => $response->getSession()->get('errors')
        ]);
    }
}
