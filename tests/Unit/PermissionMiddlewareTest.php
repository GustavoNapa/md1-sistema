<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create a permission for testing
        Permission::firstOrCreate(["name" => "access-protected-route"]);

        // Create a role for testing
        Role::firstOrCreate(["name" => "test-role"]);

        // Define a home route for redirection
        Route::get("/home", function () {
            return "Home page";
        })->name("home");

        // Define a test route protected by the permission middleware
        Route::middleware(["web", "permission:access-protected-route"])->get("/protected-route", function () {
            return "Protected content";
        });
    }

    /** @test */
    public function it_returns_403_for_unauthorized_users()
    {
        // Create a user without the required permission
        $user = User::factory()->create();
        $this->actingAs($user);

        // Attempt to access the protected route
        $response = $this->get("/protected-route");

        // Assert 403 status
        $response->assertStatus(403);
    }

    /** @test */
    public function it_allows_authorized_users()
    {
        // Create a user with the required permission
        $user = User::factory()->create();
        $role = Role::where("name", "test-role")->first();
        $role->givePermissionTo("access-protected-route");
        $user->assignRole($role);
        $this->actingAs($user);

        // Attempt to access the protected route
        $response = $this->get("/protected-route");

        // Assert successful access
        $response->assertOk();
        $this->assertEquals("Protected content", $response->getContent());
    }
}

