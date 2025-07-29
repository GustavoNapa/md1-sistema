<?php

namespace App\Listeners;

use App\Events\InscriptionCreated;
use App\Events\InscriptionUpdated;
use App\Jobs\SendInscriptionWebhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWebhookOnInscriptionEvent implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(InscriptionCreated|InscriptionUpdated $event): void
    {
        $eventType = match (get_class($event)) {
            InscriptionCreated::class => 'inscricao.created',
            InscriptionUpdated::class => 'inscricao.updated',
            default => 'inscricao.updated',
        };

        // Despachar job para envio do webhook
        SendInscriptionWebhook::dispatch($event->inscription, $eventType);
    }
}

