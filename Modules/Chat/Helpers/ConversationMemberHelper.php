<?php

namespace Modules\Chat\Helpers;

use Modules\Chat\Exceptions\ConversationMemberException;
use Modules\Chat\Models\ConversationMember;

class ConversationMemberHelper
{
    public static function getCurrentMember(mixed $conversation = null, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        $conversation = ConversationHelper::getConversation($conversation);

        if (! app()->has('conversation_member') || (app('conversation_member'))->conversation_id != $conversation->id) {
            $conversationMember = ConversationMember::query()
                ->where('conversation_id', $conversation->id)
                ->where('member_id', $userId)
                ->first();

            if (! $conversationMember) {
                ConversationMemberException::notMember();
            }

            self::setConversationMemberContainerInstance($conversationMember);
        }

        return self::getMemberContainerInstance();
    }

    public static function setConversationMemberContainerInstance(mixed $conversationMember): void
    {
        $conversationMember = $conversationMember instanceof ConversationMember ? $conversationMember : self::getCurrentMember();
        app()->forgetInstance('conversation_member');
        app()->instance('conversation_member', $conversationMember);
    }

    public static function getMemberContainerInstance()
    {
        return app('conversation_member');
    }
}
