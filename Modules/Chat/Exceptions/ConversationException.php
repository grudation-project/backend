<?php

namespace Modules\Chat\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class ConversationException extends BaseExceptionClass
{
    /**
     * @throws ConversationException
     */
    public static function alreadyExists()
    {
        throw new self(translate_error_message('conversation', 'exists'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws ConversationException
     */
    public static function maximumPinnedConversations(array $data)
    {
        throw new self(translate_word('maximum_pinned_conversations', $data), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws ConversationException
     */
    public static function notAllowedToChatWith()
    {
        throw new self(translate_word('not_allowed_to_chat_with_user'), Response::HTTP_BAD_REQUEST);
    }
}
