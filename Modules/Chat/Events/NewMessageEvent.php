<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Chat\Transformers\ConversationMessageResource;

class NewMessageEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(private $conversationId, private $message)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            'conversations.'.$this->conversationId
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => ConversationMessageResource::make($this->message),
        ];
    }
}
