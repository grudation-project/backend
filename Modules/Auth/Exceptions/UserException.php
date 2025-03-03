<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class UserException extends BaseExceptionClass
{
    /**
     * @throws UserException
     */
    public static function notExists()
    {
        throw new self(translate_error_message('user', 'not_exists'), Response::HTTP_NOT_FOUND);
    }
}
