<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionSeeder;

class PermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Limpar o cache de permissões antes de cada teste
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /** @test */
    public function it_creates_permissions_idempotently()
    {
        // Run the seeder once
        $this->seed(PermissionSeeder::class);
        $firstRunCount = Permission::count();

        // Run the seeder again
        $this->seed(PermissionSeeder::class);
        $secondRunCount = Permission::count();

        // Assert that no duplicate permissions were created
        $this->assertEquals($firstRunCount, $secondRunCount);

        // Assert that some expected permissions exist
        $this->assertDatabaseHas("permissions", ["name" => "home"]);
        $this->assertDatabaseHas("permissions", ["name" => "clients.index"]);
    }

    /** @test */
    public function it_creates_roles_and_assigns_permissions()
    {
        $this->seed(PermissionSeeder::class);

        // Assert that roles exist
        $this->assertDatabaseHas("roles", ["name" => "head-cs"]);
        $this->assertDatabaseHas("roles", ["name" => "especialista-suporte-ti"]);
        $this->assertDatabaseHas("roles", ["name" => "coordenador-mentoria"]);
        $this->assertDatabaseHas("roles", ["name" => "especialista-customer-success"]);
        $this->assertDatabaseHas("roles", ["name" => "especialista-suporte-cliente"]);

        // Assert that permissions are assigned to roles
        $headCsRole = Role::where("name", "head-cs")->first();
        $this->assertTrue($headCsRole->hasPermissionTo("home"));
        $this->assertTrue($headCsRole->hasPermissionTo("clients.index"));

        $tiRole = Role::where("name", "especialista-suporte-ti")->first();
        $this->assertTrue($tiRole->hasPermissionTo("users.index"));
        // The 'bonuses.create' permission is not assigned to 'especialista-suporte-ti' in the seeder
        // $this->assertFalse($tiRole->hasPermissionTo("bonuses.create")); 
    }
}


