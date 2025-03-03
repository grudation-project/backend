<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class SanctumTokenException extends BaseExceptionClass
{
    /**
     * @throws SanctumTokenException
     */
    public static function earlyRefresh(): self
    {
        throw new self(translate_word('early_token_refresh'), Response::HTTP_BAD_REQUEST);
    }
}
