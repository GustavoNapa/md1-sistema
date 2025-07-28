<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AchievementType;

class AchievementTypeManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_achievement_type_can_be_created()
    {
        $response = $this->postJson("/api/achievement-types", [
            "name" => "Meta de faturamento",
            "description" => "Atingir um determinado valor de faturamento.",
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("achievement_types", [
            "name" => "Meta de faturamento",
            "description" => "Atingir um determinado valor de faturamento.",
        ]);
    }

    /** @test */
    public function achievement_type_name_is_required()
    {
        $response = $this->postJson("/api/achievement-types", [
            "description" => "Atingir um determinado valor de faturamento.",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("name");
    }

    /** @test */
    public function achievement_types_can_be_retrieved()
    {
        AchievementType::factory()->create(["name" => "Meta de faturamento"]);
        AchievementType::factory()->create(["name" => "Aumento de pacientes"]);

        $response = $this->getJson("/api/achievement-types");

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(["name" => "Meta de faturamento"])
            ->assertJsonFragment(["name" => "Aumento de pacientes"]);
    }

    /** @test */
    public function an_achievement_type_can_be_updated()
    {
        $achievementType = AchievementType::factory()->create(["name" => "Meta de faturamento"]);

        $response = $this->putJson("/api/achievement-types/" . $achievementType->id, [
            "name" => "Meta de faturamento Anual",
            "description" => "Atingir um determinado valor de faturamento anual.",
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("achievement_types", [
            "id" => $achievementType->id,
            "name" => "Meta de faturamento Anual",
            "description" => "Atingir um determinado valor de faturamento anual.",
        ]);
    }

    /** @test */
    public function an_achievement_type_can_be_deleted()
    {
        $achievementType = AchievementType::factory()->create(["name" => "Meta de faturamento"]);

        $response = $this->deleteJson("/api/achievement-types/" . $achievementType->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing("achievement_types", ["id" => $achievementType->id]);
    }
}


