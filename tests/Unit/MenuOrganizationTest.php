<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class MenuOrganizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create some permissions
        Permission::firstOrCreate(["name" => "users.index"]);
        Permission::firstOrCreate(["name" => "clients.index"]);
        Permission::firstOrCreate(["name" => "feature-flags.index"]);
        Permission::firstOrCreate(["name" => "products.index"]);
        Permission::firstOrCreate(["name" => "whatsapp.index"]);
        Permission::firstOrCreate(["name" => "roles.index"]);
        Permission::firstOrCreate(["name" => "permissions.index"]);
        Permission::firstOrCreate(["name" => "webhook-logs.index"]);
        Permission::firstOrCreate(["name" => "integrations.index"]);
        Permission::firstOrCreate(["name" => "inscriptions.index"]);
        Permission::firstOrCreate(["name" => "import.index"]);

        // Create a test role
        Role::firstOrCreate(["name" => "test-role"]);
    }

    /** @test */
    public function menu_items_are_filtered_by_permission()
    {
        $user = User::factory()->create();
        $role = Role::where("name", "test-role")->first();
        $user->assignRole($role);

        // Assign only 'clients.index' permission
        $role->givePermissionTo("clients.index");

        $navigationMap = include resource_path("views/layouts/navigation_map.php");

        $visibleItems = [];
        foreach ($navigationMap as $groupName => $items) {
            foreach ($items as $item) {
                if ($user->can($item["permission"])) {
                    $visibleItems[] = $item["name"];
                }
            }
        }

        $this->assertContains("Clientes", $visibleItems);
        $this->assertNotContains("Usuários", $visibleItems);
        $this->assertNotContains("Funcionalidades", $visibleItems);
        $this->assertNotContains("Produtos", $visibleItems);
        $this->assertNotContains("WhatsApp", $visibleItems);
    }

    /** @test */
    public function menu_items_are_grouped_correctly()
    {
        $user = User::factory()->create();
        $role = Role::where("name", "test-role")->first();
        $user->assignRole($role);

        // Assign all permissions to see all groups
        $role->givePermissionTo([
            "users.index", "clients.index", "feature-flags.index", "products.index", "whatsapp.index",
            "roles.index", "permissions.index", "webhook-logs.index", "integrations.index",
            "inscriptions.index", "import.index"
        ]);

        $navigationMap = include resource_path("views/layouts/navigation_map.php");

        $expectedOrder = [
            "Administração",
            "Usuários",
            "Cargos",
            "Permissões",
            "Funcionalidades",
            "Logs de Webhooks",
            "Integrações",
            "Gestão de Clientes e Inscrições",
            "Clientes",
            "Produtos",
            "Inscrições",
            "Importação",
            "Comunicação",
            "WhatsApp",
        ];

        $actualOrder = [];
        foreach ($navigationMap as $groupName => $items) {
            $hasAccessToGroup = false;
            foreach ($items as $item) {
                if ($user->can($item["permission"])) {
                    $hasAccessToGroup = true;
                    break;
                }
            }
            if ($hasAccessToGroup) {
                $actualOrder[] = $groupName;
                foreach ($items as $item) {
                    if ($user->can($item["permission"])) {
                        $actualOrder[] = $item["name"];
                    }
                }
            }
        }
        
        $this->assertEquals($expectedOrder, $actualOrder);
    }
}


