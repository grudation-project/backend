<?php

namespace Modules\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Events\ConversationUpdatedEvent;
use Modules\Chat\Services\ConversationService;

class BulkConversationsBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $conversationId, private $users, private readonly ConversationService $conversationService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            $conversation = $this->conversationService->show($this->conversationId, $user->id);
            event(new ConversationUpdatedEvent($user->id, $conversation));
        }
    }
}
