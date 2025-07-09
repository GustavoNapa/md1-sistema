<?php

namespace App\Listeners;

use App\Events\InscriptionCreated;
use App\Jobs\SendExternalWebhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendInscriptionWebhook
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InscriptionCreated $event): void
    {
        $inscription = $event->inscription;
        $inscription->load(['client', 'vendor']);

        // URLs de webhook configuradas (podem vir do .env ou banco de dados)
        $webhookUrls = [
            config('app.external_webhook_url'),
            // Adicionar mais URLs conforme necessário
        ];

        // Dados a serem enviados no webhook
        $webhookData = [
            'inscription' => [
                'id' => $inscription->id,
                'product' => $inscription->product,
                'class_group' => $inscription->class_group,
                'status' => $inscription->status,
                'start_date' => $inscription->start_date?->toDateString(),
                'amount_paid' => $inscription->amount_paid,
                'payment_method' => $inscription->payment_method,
            ],
            'client' => [
                'id' => $inscription->client->id,
                'name' => $inscription->client->name,
                'email' => $inscription->client->email,
                'cpf' => $inscription->client->cpf,
                'specialty' => $inscription->client->specialty,
            ],
            'vendor' => $inscription->vendor ? [
                'id' => $inscription->vendor->id,
                'name' => $inscription->vendor->name,
            ] : null,
        ];

        // Enviar webhook para cada URL configurada
        foreach ($webhookUrls as $url) {
            if ($url) {
                SendExternalWebhook::dispatch($url, $webhookData, 'inscription.created');
                
                Log::info('Webhook de inscrição agendado', [
                    'inscription_id' => $inscription->id,
                    'webhook_url' => $url
                ]);
            }
        }
    }
}
