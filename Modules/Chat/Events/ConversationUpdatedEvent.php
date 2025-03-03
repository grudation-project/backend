<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Chat\Transformers\ConversationResource;

class ConversationUpdatedEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, Queueable;

    public function __construct(private $userId, private $conversation)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.'.$this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'conversation-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation' => ConversationResource::make($this->conversation),
        ];
    }
}
