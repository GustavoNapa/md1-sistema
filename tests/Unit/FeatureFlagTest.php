<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;

class FeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Certifique-se de que o cache está limpo antes de cada teste
        Cache::forget("feature:test_feature");
        Cache::forget("feature:another_feature");
        Cache::forget("feature:ChatWhatsApp");
        Cache::forget("feature:KanbanInscricoes");

        // Criar o role \'admin\' se não existir
        if (! Role::where("name", "admin")->exists()) {
            Role::create(["name" => "admin"]);
        }

        // Definir a rota de toggle de feature flag para o teste
        Route::post("api/feature-flags/{featureKey}/toggle", [\App\Http\Controllers\FeatureFlagController::class, "toggle"])->name("feature-flags.toggle");
    }

    /** @test */
    public function a_feature_flag_can_be_created_and_retrieved()
    {
        $featureFlag = FeatureFlag::create([
            "key" => "test_feature",
            "enabled" => true,
            "user_id" => null,
        ]);

        $this->assertDatabaseHas("feature_flags", [
            "key" => "test_feature",
            "enabled" => true,
            "user_id" => null,
        ]);

        $retrievedFlag = FeatureFlag::where("key", "test_feature")->first();
        $this->assertTrue($retrievedFlag->enabled);
    }

    /** @test */
    public function a_feature_flag_can_be_disabled()
    {
        $featureFlag = FeatureFlag::create([
            "key" => "another_feature",
            "enabled" => true,
            "user_id" => null,
        ]);

        $featureFlag->enabled = false;
        $featureFlag->save();

        $this->assertFalse($featureFlag->fresh()->enabled);
    }

    /** @test */
    public function it_activates_feature_and_updates_cache_and_logs() // Cenário: Ativar feature
    {
        // Criar um usuário admin para simular a ação
        $adminUser = User::factory()->create();
        $adminUser->assignRole("admin"); // Assumindo que existe um role \'admin\'
        $this->actingAs($adminUser);

        // Criar a feature flag no banco de dados
        FeatureFlag::create([
            "key" => "ChatWhatsApp",
            "enabled" => false,
            "user_id" => null,
        ]);

        // Desabilitar verificação CSRF para o teste
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Simular a requisição para ativar a feature
        $response = $this->postJson("/api/feature-flags/ChatWhatsApp/toggle", [
            "enabled" => true
        ]);

        $response->assertStatus(200);
        $response->assertJson(["success" => true, "enabled" => true]);

        // Verificar se a feature está ativa no banco de dados
        $this->assertTrue(FeatureFlag::where("key", "ChatWhatsApp")->first()->enabled);

        // Verificar se a feature está ativa no cache (Pennant)
        $this->assertTrue(\Laravel\Pennant\Feature::active("ChatWhatsApp"));

        // TODO: Verificar log em feature_flag_logs (requer implementação da tabela de logs)
    }

    /** @test */
    public function it_redirects_when_accessing_blocked_route() // Cenário: Rota bloqueada
    {
        // Criar a feature flag no banco de dados e desativá-la
        FeatureFlag::create([
            "key" => "KanbanInscricoes",
            "enabled" => false,
            "user_id" => null,
        ]);

        // Simular acesso à rota protegida pelo middleware
        // Para este teste, precisamos de uma rota real protegida pelo middleware \'feature\'
        // Vamos adicionar uma rota temporária para teste
        
        // Adicionar uma rota de teste que usa o middleware \'feature\'
        Route::middleware(["web", "feature:KanbanInscricoes"])->get("/test-kanban", function () {
            return "Kanban page";
        });

        $response = $this->get("/test-kanban");

        $response->assertRedirect(route("home"));
        $response->assertSessionHas("error", "Funcionalidade \"KanbanInscricoes\" desativada.");
    }
}


