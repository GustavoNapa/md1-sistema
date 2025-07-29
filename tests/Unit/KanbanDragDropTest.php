<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KanbanDragDropTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_inscription_status_when_card_is_moved_to_new_column()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $inscription = Inscription::factory()->create([
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/inscriptions/{$inscription->id}/move", [
            'field' => 'status',
            'value' => 'completed',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);

        $this->assertEquals('completed', $inscription->fresh()->status);
    }
}


