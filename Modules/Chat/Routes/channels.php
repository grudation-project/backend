<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Modules\Chat\Helpers\ChatUserHelper;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Conversation;

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return $user->id == $userId && ChatUserHelper::allowedToChat();
});

Broadcast::channel('conversations.{conversationId}', function (User $user, $conversationId) {
    Conversation::query()
        ->when(true, fn (ConversationBuilder $b) => $b->whereMember($user->id))
        ->findOrFail($conversationId);

    return true;
});
