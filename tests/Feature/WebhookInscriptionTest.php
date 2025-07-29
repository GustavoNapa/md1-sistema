<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Inscription;
use App\Models\Product;
use App\Models\Client;
use App\Models\ProductWebhook;
use Database\Factories\ClientFactory;
use Database\Factories\ProductFactory;
use Database\Factories\ProductWebhookFactory;
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

        // Manually run the job to test the HTTP request
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
        $job->tries = 1; // Simulate first attempt

        try {
            $job->handle();
        } catch (\Exception $e) {
            // Expected to fail on first attempt
        }

        // Assert that the job was released back to the queue for retry
        Queue::assertPushed(ProcessInscriptionWebhook::class, function ($job) {
            return $job->attempts() === 1; // Check if it's the first retry
        });

        // Simulate a retry by creating a new job instance and running it
        $retriedJob = new ProcessInscriptionWebhook($inscription, $this->productWebhook);
        $retriedJob->tries = 2; // Simulate second attempt
        $retriedJob->handle();

        Http::assertSentCount(2); // Two requests should have been sent
    }
}


