<?php

namespace Modules\Chat\Listeners;

use App\Models\User;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Conversation;

class DeleteConversationListener
{
    public function handle(User $user): void
    {
        Conversation::query()
            ->withoutGlobalScopes()
            ->when(true, fn(ConversationBuilder $b) => $b->whereMember($user->id, true))
            ->delete();
    }
}
