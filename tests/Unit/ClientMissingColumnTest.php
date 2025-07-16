<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\QueryException;

class ClientMissingColumnTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to simulate the error: SQLSTATE[HY000]: General error: 1 table clients has no column named name
     * 
     * This test temporarily drops the 'name' column from clients table to simulate the error scenario
     */
    public function test_client_creation_fails_when_name_column_missing()
    {
        // First, let's create the clients table normally
        $this->artisan('migrate');
        
        // Verify the name column exists initially
        $this->assertTrue(Schema::hasColumn('clients', 'name'));
        
        // Temporarily drop the name column to simulate the error
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('name');
        });
        
        // Verify the name column is now missing
        $this->assertFalse(Schema::hasColumn('clients', 'name'));
        
        // Now try to create a client that includes the 'name' field
        // This should throw a QueryException about missing column
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('has no column named name');
        
        Client::create([
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'birth_date' => '1990-01-01',
            'specialty' => 'Medicina',
            'service_city' => 'São Paulo',
            'state' => 'SP',
            'phone' => '11999999999',
            'active' => true
        ]);
    }

    /**
     * Test to verify that mass assignment works when name column exists
     */
    public function test_client_creation_succeeds_when_name_column_exists()
    {
        // Run migrations to ensure proper table structure
        $this->artisan('migrate');
        
        // Verify the name column exists
        $this->assertTrue(Schema::hasColumn('clients', 'name'));
        
        // Create a client successfully
        $client = Client::create([
            'name' => 'Maria Santos',
            'cpf' => '98765432100',
            'email' => 'maria@example.com',
            'birth_date' => '1985-05-15',
            'specialty' => 'Odontologia',
            'service_city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'phone' => '21988888888',
            'active' => true
        ]);
        
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Maria Santos', $client->name);
        $this->assertEquals('98765432100', $client->cpf);
        $this->assertEquals('maria@example.com', $client->email);
    }

    /**
     * Test to simulate what happens when trying to access name attribute on a client
     * when the column doesn't exist in the database
     */
    public function test_client_name_access_fails_when_column_missing()
    {
        // First create a client with name column present
        $this->artisan('migrate');
        
        $client = Client::create([
            'name' => 'Pedro Costa',
            'cpf' => '11122233344',
            'email' => 'pedro@example.com',
            'phone' => '11977777777',
            'active' => true
        ]);
        
        // Now drop the name column
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('name');
        });
        
        // Try to access the name attribute - this should cause issues
        $freshClient = Client::find($client->id);
        
        // The name attribute won't be available in the fresh model
        $this->assertNull($freshClient->name ?? null);
        
        // Trying to update with name should fail
        $this->expectException(QueryException::class);
        $freshClient->update(['name' => 'Pedro Silva']);
    }

    /**
     * Test database schema integrity
     */
    public function test_clients_table_has_required_columns()
    {
        $this->artisan('migrate');
        
        // Verify all expected columns exist
        $expectedColumns = [
            'id', 'name', 'cpf', 'email', 'birth_date', 
            'specialty', 'service_city', 'state', 'region', 
            'instagram', 'phone', 'active', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('clients', $column),
                "Column '{$column}' is missing from clients table"
            );
        }
    }
}
