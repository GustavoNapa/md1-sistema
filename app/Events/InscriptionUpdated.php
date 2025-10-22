<?php

namespace App\Events;

use App\Models\Inscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class InscriptionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inscription;
    public $eventType; // ex: 'inscricao.updated'

    /**
     * Create a new event instance.
     */
    public function __construct(Inscription $inscription, string $eventType = 'inscricao.updated')
    {
        $this->inscription = $inscription;
        $this->eventType = $eventType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

