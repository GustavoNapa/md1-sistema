<?php

namespace App\Events;

use App\Models\WhatsappMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(WhatsappMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('whatsapp.conversation.' . $this->message->conversation_id),
            new PrivateChannel('whatsapp.global'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.received';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message_id' => $this->message->message_id,
                'conversation_id' => $this->message->conversation_id,
                'direction' => $this->message->direction,
                'type' => $this->message->type,
                'content' => $this->message->content,
                'status' => $this->message->status,
                'status_icon' => $this->message->getStatusIcon(),
                'created_at' => $this->message->created_at->format('H:i'),
                'from_phone' => $this->message->from_phone,
            ],
            'conversation' => [
                'id' => $this->message->conversation->id,
                'contact_name' => $this->message->conversation->contact_name,
                'contact_phone' => $this->message->conversation->contact_phone,
                'unread_count' => $this->message->conversation->unread_count,
            ],
        ];
    }
}

