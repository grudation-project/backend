<?php

namespace Modules\Manager\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class ManagerException extends BaseExceptionClass
{
    /**
     * @throws ManagerException
     */
    public static function notExists()
    {
        throw new self(translate_error_message('manager', 'not_exists'), Response::HTTP_NOT_FOUND);
    }
}
