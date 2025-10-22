<?php

namespace App\Events;

use App\Models\Inscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class InscriptionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inscription;
    public $eventType; // ex: 'inscricao.created'

    /**
     * Create a new event instance.
     */
    public function __construct(Inscription $inscription, string $eventType = 'inscricao.created')
    {
        $this->inscription = $inscription;
        $this->eventType = $eventType;
    }

    /**
     * (kept for compatibility; not used for broadcasting by default)
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
