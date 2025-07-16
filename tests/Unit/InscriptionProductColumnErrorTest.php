<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Inscription;
use App\Models\Client;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class InscriptionProductColumnErrorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to document that the error was FIXED: NOT NULL constraint failed: inscriptions.product
     * 
     * This error USED TO occur because we had both 'product' (string, NOT NULL) and 'product_id' (foreign key)
     * columns in the inscriptions table, but only 'product_id' was being filled.
     * 
     * This test now verifies that the problem is RESOLVED.
     */
    public function test_inscription_creation_no_longer_fails_product_column_removed()
    {
        // Run migrations to set up the database
        $this->artisan('migrate');
        
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

        // Verify that the old 'product' column no longer exists (FIXED!)
        $this->assertFalse(Schema::hasColumn('inscriptions', 'product'), 'Old product column should be removed');
        // But product_id should still exist
        $this->assertTrue(Schema::hasColumn('inscriptions', 'product_id'), 'product_id column should exist');

        // Create inscription with only product_id - this should now WORK
        $inscription = Inscription::create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'has_medboss' => true,
        ]);

        // Verify it was created successfully
        $this->assertInstanceOf(Inscription::class, $inscription);
        $this->assertEquals($client->id, $inscription->client_id);
        $this->assertEquals($product->id, $inscription->product_id);
    }

    /**
     * Test to verify the structure problem is FIXED: we no longer have redundant product columns
     */
    public function test_inscriptions_table_no_longer_has_redundant_product_columns()
    {
        $this->artisan('migrate');
        
        // The old 'product' column should no longer exist (FIXED!)
        $this->assertFalse(Schema::hasColumn('inscriptions', 'product'), 'Old product column should be removed');
        // But product_id should still exist
        $this->assertTrue(Schema::hasColumn('inscriptions', 'product_id'), 'product_id column should exist');
        
        // No more redundancy - problem solved!
    }

    /**
     * Test what happens when we access the product through relationship (FIXED VERSION)
     */
    public function test_inscription_product_relationship_works_after_fix()
    {
        $this->artisan('migrate');
        
        // Create required related models
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
        
        // Now we access product through relationship, not the old string column
        $this->assertInstanceOf(Product::class, $inscription->product);
        $this->assertEquals($product->name, $inscription->product->name);
    }

    /**
     * Test to verify the fillable fields in Inscription model (UPDATED)
     */
    public function test_inscription_model_fillable_fields_after_fix()
    {
        $inscription = new Inscription();
        $fillable = $inscription->getFillable();
        
        // Check that product_id is in fillable (should be)
        $this->assertContains('product_id', $fillable, 'product_id should be fillable');
        
        // The old 'product' field should NOT be in fillable (and column doesn't exist anymore)
        $this->assertNotContains('product', $fillable, 'Old product field should not be in fillable');
    }

    /**
     * Test simulating the exact bug report scenario - NOW FIXED
     */
    public function test_exact_bug_report_scenario_now_works()
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

        // This should now WORK (no more error!)
        $inscription = Inscription::create($inscriptionData);
        
        // Verify it was created successfully
        $this->assertInstanceOf(Inscription::class, $inscription);
        $this->assertEquals($client->id, $inscription->client_id);
        $this->assertEquals($product->id, $inscription->product_id);
    }
}
