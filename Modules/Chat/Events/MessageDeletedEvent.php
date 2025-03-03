<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;

class MessageDeletedEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(private $conversationId, private $messageId) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversations.'.$this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message-deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'messageId' => $this->messageId,
        ];
    }
}
