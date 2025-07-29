<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Inscription;
use App\Models\Product;
use App\Models\Client;
use App\Models\ProductWebhook;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessInscriptionWebhook;

class WebhookInscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um cliente de teste
        $this->client = Client::factory()->create();

        // Criar um produto de teste
        $this->product = Product::factory()->create([
            'name' => 'Produto Teste',
            'is_active' => true,
        ]);

        // Criar um webhook de produto de teste
        $this->productWebhook = ProductWebhook::factory()->create([
            'product_id' => $this->product->id,
            'webhook_url' => 'https://webhook.site/test-webhook',
            'webhook_token' => 'test_token',
            'webhook_trigger_status' => 'active',
        ]);
    }

    /** @test */
    public function it_dispatches_webhook_on_inscription_creation_with_matching_status()
    {
        Queue::fake();
        Http::fake();

        $inscription = Inscription::factory()->create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'status' => 'active',
        ]);

        // Assert that the job was dispatched
        Queue::assertPushed(ProcessInscriptionWebhook::class, function ($job) use ($inscription) {
            return $job->inscription->id === $inscription->id &&
                   $job->productWebhook->id === $this->productWebhook->id;
        });

        // Manually run the job to test the HTTP request and webhook log creation
        $job = new ProcessInscriptionWebhook($inscription, $this->productWebhook);
        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://webhook.site/test-webhook' &&
                   $request->method() === 'POST' &&
                   $request->header('Authorization')[0] === 'Bearer test_token' &&
                   $request->header('Content-Type')[0] === 'application/json' &&
                   isset($request['client']) &&
                   isset($request['inscription']) &&
                   isset($request['mapping']);
        });

        // Assert webhook log was created and updated correctly
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'webhook_url' => $this->productWebhook->webhook_url,
            'event_type' => 'inscription_updated',
            'attempt_number' => 1,
            'status' => 'success',
            'response_status' => 200,
        ]);
    }

    /** @test */
    public function it_does_not_dispatch_webhook_on_inscription_creation_with_non_matching_status()
    {
        Queue::fake();
        Http::fake();

        $inscription = Inscription::factory()->create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'status' => 'pending',
        ]);

        // Assert that no job was pushed
        Queue::assertNotPushed(ProcessInscriptionWebhook::class);

        Http::assertNothingSent();

        // Assert no webhook log was created
        $this->assertDatabaseMissing('webhook_logs', [
            'inscription_id' => $inscription->id,
        ]);
    }

    /** @test */
    public function it_retries_webhook_dispatch_on_failure()
    {
        Queue::fake();
        Http::fakeSequence()
            ->pushStatus(500) // Simulate failure
            ->pushStatus(200); // Simulate success on retry

        $inscription = Inscription::factory()->create([
            'client_id' => $this->client->id,
            'product_id' => $this->product->id,
            'status' => 'active',
        ]);

        // Manually run the job to simulate the first attempt
        $job = new ProcessInscriptionWebhook($inscription, $this->productWebhook);
        $job->attempts = 1; // Simulate first attempt

        try {
            $job->handle();
        } catch (\Exception $e) {
            // Expected to fail on first attempt
        }

        // Assert webhook log for first attempt
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'webhook_url' => $this->productWebhook->webhook_url,
            'event_type' => 'inscription_updated',
            'attempt_number' => 1,
            'status' => 'failed',
            'response_status' => 500,
        ]);

        // Simulate a retry by creating a new job instance and running it
        $retriedJob = new ProcessInscriptionWebhook($inscription, $this->productWebhook);
        $retriedJob->attempts = 2; // Simulate second attempt
        $retriedJob->handle();

        Http::assertSentCount(2); // Two requests should have been sent

        // Assert webhook log for the successful retry (it updates the existing log)
        $this->assertDatabaseHas('webhook_logs', [
            'inscription_id' => $inscription->id,
            'webhook_url' => $this->productWebhook->webhook_url,
            'event_type' => 'inscription_updated',
            'attempt_number' => 1, // Still attempt 1 as it's updated
            'status' => 'success',
            'response_status' => 200,
        ]);
    }
}


