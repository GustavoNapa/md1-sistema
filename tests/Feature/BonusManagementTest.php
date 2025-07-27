<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Subscription;

class BonusManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_bonus_can_be_added_to_a_subscription()
    {
        // Given: A subscription exists
        $subscription = Subscription::factory()->create();

        // When: A bonus is added to the subscription
        $response = $this->postJson("/api/subscriptions/" . $subscription->id . "/bonuses", [
            "description" => "3 meses de serviço de tráfego pago",
            "release_date" => "2025-07-27",
            "expiration_date" => "2025-10-27",
        ]);

        // Then: The bonus is stored in the database and the response is successful
        $response->assertStatus(201);
        $this->assertDatabaseHas("bonuses", [
            "subscription_id" => $subscription->id,
            "description" => "3 meses de serviço de tráfego pago",
            "release_date" => "2025-07-27",
            "expiration_date" => "2025-10-27",
        ]);
    }

    /** @test */
    public function a_bonus_can_be_added_without_expiration_date()
    {
        // Given: A subscription exists
        $subscription = Subscription::factory()->create();

        // When: A bonus is added to the subscription without an expiration date
        $response = $this->postJson("/api/subscriptions/" . $subscription->id . "/bonuses", [
            "description" => "Acesso vitalício ao CRM",
            "release_date" => "2025-07-27",
        ]);

        // Then: The bonus is stored in the database with a null expiration date and the response is successful
        $response->assertStatus(201);
        $this->assertDatabaseHas("bonuses", [
            "subscription_id" => $subscription->id,
            "description" => "Acesso vitalício ao CRM",
            "release_date" => "2025-07-27",
            "expiration_date" => null,
        ]);
    }

    /** @test */
    public function description_is_required_to_add_a_bonus()
    {
        // Given: A subscription exists
        $subscription = Subscription::factory()->create();

        // When: A bonus is added without a description
        $response = $this->postJson("/api/subscriptions/" . $subscription->id . "/bonuses", [
            "release_date" => "2025-07-27",
        ]);

        // Then: The response indicates a validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors("description");
    }

    /** @test */
    public function release_date_is_required_to_add_a_bonus()
    {
        // Given: A subscription exists
        $subscription = Subscription::factory()->create();

        // When: A bonus is added without a release date
        $response = $this->postJson("/api/subscriptions/" . $subscription->id . "/bonuses", [
            "description" => "Test Bonus",
        ]);

        // Then: The response indicates a validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors("release_date");
    }
}


