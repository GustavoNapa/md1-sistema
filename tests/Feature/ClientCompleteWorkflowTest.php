<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Specialty;

class ClientCompleteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        
        // Criar especialidades do CFM para teste
        $this->seedSpecialties();
    }

    /** @test */
    public function complete_client_creation_workflow_with_all_validations()
    {
        // 1. Acessar página de criação
        $response = $this->actingAs($this->user)
            ->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('Novo Cliente');
        $response->assertSee('Selecione uma especialidade');

        // 2. Tentar criar com dados inválidos
        $invalidData = [
            'name' => '',
            'email' => 'email-invalido',
            'cpf' => '',
            'phone' => 'texto apenas',
            'birth_date' => now()->addDay()->format('Y-m-d'),
            'specialty' => 'Especialidade Inexistente',
            'service_city' => '123456',
            'state' => 'XX',
            'region' => '999'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), $invalidData);

        $response->assertSessionHasErrors([
            'name', 'email', 'cpf', 'phone', 'birth_date', 
            'specialty', 'service_city', 'state', 'region'
        ]);

        // 3. Criar com dados válidos
        $validData = [
            'name' => 'Dr. João Silva',
            'email' => 'joao.silva@exemplo.com',
            'cpf' => '123.456.789-00',
            'phone' => '(11) 98765-4321',
            'birth_date' => '1985-06-15',
            'specialty' => 'Cardiologia',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste',
            'instagram' => '@drjoao'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), $validData);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        // 4. Verificar se foi salvo corretamente
        $this->assertDatabaseHas('clients', [
            'name' => 'Dr. João Silva',
            'email' => 'joao.silva@exemplo.com',
            'specialty' => 'Cardiologia',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'region' => 'Sudeste'
        ]);

        $client = Client::where('email', 'joao.silva@exemplo.com')->first();
        $this->assertNotNull($client);
    }

    /** @test */
    public function complete_client_edit_workflow_with_all_validations()
    {
        // 1. Criar cliente inicial
        $client = Client::create([
            'name' => 'Dr. Maria Santos',
            'email' => 'maria@exemplo.com',
            'cpf' => '987.654.321-00',
            'specialty' => 'Dermatologia'
        ]);

        // 2. Acessar página de edição
        $response = $this->actingAs($this->user)
            ->get(route('clients.edit', $client));

        $response->assertStatus(200);
        $response->assertSee('Editar Cliente');
        $response->assertSee($client->name);
        $response->assertSee('selected'); // Verificar que a especialidade está selecionada

        // 3. Tentar atualizar com dados inválidos
        $invalidUpdateData = [
            'name' => '',
            'email' => 'email-invalido',
            'cpf' => '987.654.321-00',
            'phone' => 'abc123',
            'birth_date' => now()->addDays(5)->format('Y-m-d'),
            'specialty' => 'Especialidade Falsa',
            'service_city' => '999999',
            'state' => 'ZZ',
            'region' => '777',
            'active' => 1
        ];

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), $invalidUpdateData);

        $response->assertSessionHasErrors([
            'name', 'email', 'phone', 'birth_date',
            'specialty', 'service_city', 'state', 'region'
        ]);

        // 4. Atualizar com dados válidos
        $validUpdateData = [
            'name' => 'Dra. Maria Santos Oliveira',
            'email' => 'maria.oliveira@exemplo.com',
            'cpf' => '987.654.321-00',
            'phone' => '(21) 99888-7766',
            'birth_date' => '1980-03-20',
            'specialty' => 'Cardiologia',
            'service_city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'region' => 'Sudeste',
            'instagram' => '@dramaria',
            'active' => 1
        ];

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), $validUpdateData);

        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('success');

        // 5. Verificar se foi atualizado corretamente
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Dra. Maria Santos Oliveira',
            'email' => 'maria.oliveira@exemplo.com',
            'specialty' => 'Cardiologia',
            'state' => 'RJ',
            'region' => 'Sudeste'
        ]);
    }

    /** @test */
    public function phone_mask_validation_comprehensive_test()
    {
        $phoneTestCases = [
            // Válidos
            ['phone' => '(11) 98765-4321', 'should_pass' => true],
            ['phone' => '(21) 99999-9999', 'should_pass' => true],
            ['phone' => '11987654321', 'should_pass' => true],
            ['phone' => '(85) 3456-7890', 'should_pass' => true],
            
            // Inválidos
            ['phone' => 'abc123def', 'should_pass' => false],
            ['phone' => 'telefone', 'should_pass' => false],
            ['phone' => '123', 'should_pass' => false],
        ];

        foreach ($phoneTestCases as $testCase) {
            $data = [
                'name' => 'Teste Phone',
                'email' => 'teste.phone' . rand(1000, 9999) . '@exemplo.com',
                'cpf' => '111.222.333-' . rand(10, 99),
                'phone' => $testCase['phone'],
                'specialty' => 'Cardiologia'
            ];

            $response = $this->actingAs($this->user)
                ->post(route('clients.store'), $data);

            if ($testCase['should_pass']) {
                $response->assertRedirect(route('clients.index'));
                $this->assertDatabaseHas('clients', [
                    'phone' => $testCase['phone']
                ]);
            } else {
                $response->assertSessionHasErrors('phone');
            }
        }
    }

    /** @test */
    public function specialty_integration_with_cfm_data_test()
    {
        // Verificar se as especialidades do CFM estão disponíveis
        $this->assertDatabaseHas('specialties', ['name' => 'Cardiologia']);
        $this->assertDatabaseHas('specialties', ['name' => 'Dermatologia']);
        $this->assertDatabaseHas('specialties', ['name' => 'Neurologia']);

        // Criar cliente com especialidade válida do CFM
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'Dr. Especialista',
                'email' => 'especialista@cfm.com',
                'cpf' => '444.555.666-77',
                'specialty' => 'Cardiologia'
            ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'specialty' => 'Cardiologia'
        ]);

        // Tentar criar com especialidade inexistente
        $response = $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name' => 'Dr. Falso',
                'email' => 'falso@cfm.com',
                'cpf' => '888.999.000-11',
                'specialty' => 'Especialidade Inventada'
            ]);

        $response->assertSessionHasErrors('specialty');
    }

    /** @test */
    public function geographic_validation_comprehensive_test()
    {
        $geographicTestCases = [
            // Casos válidos
            [
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => 'Sudeste',
                'should_pass' => true
            ],
            [
                'service_city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'region' => 'Sudeste',
                'should_pass' => true
            ],
            [
                'service_city' => 'Salvador',
                'state' => 'BA',
                'region' => 'Nordeste',
                'should_pass' => true
            ],
            
            // Casos inválidos
            [
                'service_city' => '123456', // Só números
                'state' => 'SP',
                'region' => 'Sudeste',
                'should_pass' => false,
                'error_field' => 'service_city'
            ],
            [
                'service_city' => 'São Paulo',
                'state' => 'XX', // UF inválida
                'region' => 'Sudeste',
                'should_pass' => false,
                'error_field' => 'state'
            ],
            [
                'service_city' => 'São Paulo',
                'state' => 'SP',
                'region' => '999', // Região só números
                'should_pass' => false,
                'error_field' => 'region'
            ]
        ];

        foreach ($geographicTestCases as $index => $testCase) {
            $data = [
                'name' => "Cliente Geo {$index}",
                'email' => "geo{$index}@exemplo.com",
                'cpf' => "555.666.777-" . str_pad($index, 2, '0', STR_PAD_LEFT),
                'service_city' => $testCase['service_city'],
                'state' => $testCase['state'],
                'region' => $testCase['region'],
                'specialty' => 'Cardiologia'
            ];

            $response = $this->actingAs($this->user)
                ->post(route('clients.store'), $data);

            if ($testCase['should_pass']) {
                $response->assertRedirect(route('clients.index'));
            } else {
                $response->assertSessionHasErrors($testCase['error_field']);
            }
        }
    }

    private function seedSpecialties()
    {
        $specialties = [
            ['name' => 'Cardiologia', 'cfm_code' => 'CAR'],
            ['name' => 'Dermatologia', 'cfm_code' => 'DER'],
            ['name' => 'Neurologia', 'cfm_code' => 'NEU'],
            ['name' => 'Oftalmologia', 'cfm_code' => 'OFT'],
            ['name' => 'Ortopedia', 'cfm_code' => 'ORT'],
            ['name' => 'Pediatria', 'cfm_code' => 'PED'],
            ['name' => 'Psiquiatria', 'cfm_code' => 'PSI'],
            ['name' => 'Ginecologia', 'cfm_code' => 'GIN'],
            ['name' => 'Urologia', 'cfm_code' => 'URO'],
            ['name' => 'Anestesiologia', 'cfm_code' => 'ANE']
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
