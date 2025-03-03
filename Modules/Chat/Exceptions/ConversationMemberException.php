<?php

namespace Modules\Chat\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ConversationMemberException extends Exception
{
    /**
     * @throws ConversationMemberException
     */
    public static function notMember()
    {
        throw new self(translate_word('not_member_conversation'), Response::HTTP_NOT_FOUND);
    }
}
