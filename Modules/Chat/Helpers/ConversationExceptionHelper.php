<?php

namespace Modules\Chat\Helpers;

use App\Helpers\BaseExceptionHelper;
use Illuminate\Foundation\Configuration\Exceptions;
use Modules\Chat\Exceptions\ConversationException;
use Modules\Chat\Exceptions\ConversationMemberException;

class ConversationExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(fn (ConversationException $e) => self::generalErrorResponse($e));
        $exceptions->renderable(fn (ConversationMemberException $e) => self::generalErrorResponse($e));
    }
}
