<?php

namespace Modules\Chat\Helpers;

use Modules\Chat\Models\Conversation;

class ConversationHelper
{
    private static function findConversation($conversation)
    {
        $conversation = ! is_null($conversation) ? $conversation : request()->route('conversationId');

        return Conversation::query()->findOrFail($conversation);
    }

    public static function setConversationContainerInstance(mixed $conversation): void
    {
        $conversation = $conversation instanceof Conversation ? $conversation : self::findConversation($conversation);

        app()->forgetInstance('conversation');
        app()->instance('conversation', $conversation);
    }

    public static function getConversation(mixed $conversation): Conversation
    {
        if ($conversation instanceof Conversation) {
            return $conversation;
        }

        if (! app()->has('conversation')) {
            $newConversation = self::findConversation($conversation);

            self::setConversationContainerInstance($newConversation);
        }

        return app('conversation');
    }
}
