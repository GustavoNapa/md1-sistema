<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Inscription;

class InscriptionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that inscription creation works through HTTP request after the fix
     */
    public function test_inscription_creation_via_http_post_works()
    {
        // Run migrations
        $this->artisan('migrate');
        
        // Create a user to authenticate
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create required related models
        $client = Client::create([
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'active' => true
        ]);

        $product = Product::create([
            'name' => 'Produto Teste',
            'description' => 'Descrição do produto',
            'price' => 100.00,
            'offer_price' => 80.00,
            'is_active' => true
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Make POST request to create inscription (simulate the exact scenario from the bug report)
        $response = $this->post('/inscriptions', [
            'client_id' => $client->id,
            'vendor_id' => null,
            'product_id' => $product->id,
            'class_group' => null,
            'status' => 'active',
            'classification' => null,
            'has_medboss' => true,
            'crmb_number' => null,
            'start_date' => null,
            'original_end_date' => null,
            'actual_end_date' => null,
            'platform_release_date' => null,
            'calendar_week' => null,
            'current_week' => null,
            'amount_paid' => null,
            'payment_method' => null,
            'commercial_notes' => null,
            'general_notes' => null,
        ]);

        // Should redirect to show page (successful creation)
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Inscrição criada com sucesso!');

        // Verify inscription was created in database
        $this->assertDatabaseHas('inscriptions', [
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'has_medboss' => true,
        ]);

        // Verify the inscription was created and has the right relationships
        $inscription = Inscription::where('client_id', $client->id)->first();
        $this->assertNotNull($inscription);
        $this->assertEquals($product->id, $inscription->product_id);
        $this->assertEquals($client->id, $inscription->client_id);
        
        // Test the relationship works
        $this->assertEquals($product->name, $inscription->product->name);
        $this->assertEquals($client->name, $inscription->client->name);
    }

    /**
     * Test accessing the inscription create form
     */
    public function test_inscription_create_form_loads()
    {
        $this->artisan('migrate');
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/inscriptions/create');
        $response->assertStatus(200);
        $response->assertViewIs('inscriptions.create');
    }

    /**
     * Test accessing the inscription index
     */
    public function test_inscription_index_loads()
    {
        $this->artisan('migrate');
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/inscriptions');
        $response->assertStatus(200);
        $response->assertViewIs('inscriptions.index');
    }
}
