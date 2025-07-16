<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Inscription;
use App\Models\Client;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class InscriptionCreateAfterFixTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that inscription creation works after removing the product column
     */
    public function test_inscription_creation_works_after_fix()
    {
        // Run migrations
        $this->artisan('migrate');
        
        // Verify that the old 'product' column no longer exists
        $this->assertFalse(Schema::hasColumn('inscriptions', 'product'), 'Old product column should be removed');
        
        // Verify that 'product_id' column still exists
        $this->assertTrue(Schema::hasColumn('inscriptions', 'product_id'), 'product_id column should exist');
        
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

        // Create inscription with only product_id (should work now)
        $inscription = Inscription::create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'has_medboss' => true,
        ]);

        $this->assertInstanceOf(Inscription::class, $inscription);
        $this->assertEquals($client->id, $inscription->client_id);
        $this->assertEquals($product->id, $inscription->product_id);
        $this->assertEquals('active', $inscription->status);
    }

    /**
     * Test simulating the exact bug report scenario after fix
     */
    public function test_exact_bug_report_scenario_works_after_fix()
    {
        $this->artisan('migrate');
        
        // Create the exact data from the bug report
        $client = Client::create([
            'name' => 'Test Client',
            'cpf' => '11111111111',
            'email' => 'test@example.com',
            'phone' => '11999999999',
            'active' => true
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100.00,
            'is_active' => true
        ]);

        // Simulate the exact data being sent in the POST request
        $inscriptionData = [
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
        ];

        // This should work now without the NOT NULL constraint error
        $inscription = Inscription::create($inscriptionData);

        $this->assertInstanceOf(Inscription::class, $inscription);
        $this->assertEquals($client->id, $inscription->client_id);
        $this->assertEquals($product->id, $inscription->product_id);
        $this->assertEquals('active', $inscription->status);
    }

    /**
     * Test that the product relationship works correctly
     */
    public function test_product_relationship_works()
    {
        $this->artisan('migrate');
        
        $client = Client::create([
            'name' => 'Maria Santos',
            'cpf' => '98765432100',
            'email' => 'maria@example.com',
            'phone' => '21999999999',
            'active' => true
        ]);

        $product = Product::create([
            'name' => 'Curso de Laravel',
            'description' => 'Curso completo de Laravel',
            'price' => 200.00,
            'offer_price' => 150.00,
            'is_active' => true
        ]);

        $inscription = Inscription::create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'has_medboss' => true,
        ]);

        // Test the relationship
        $this->assertInstanceOf(Product::class, $inscription->product);
        $this->assertEquals($product->name, $inscription->product->name);
        $this->assertEquals($product->price, $inscription->product->price);
    }
}
