<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenException extends BaseExceptionClass
{
    /**
     * @throws RefreshTokenException
     */
    public static function expired()
    {
        throw new self(translate_word('refresh_token_expired'), Response::HTTP_UNAUTHORIZED);
    }
}
