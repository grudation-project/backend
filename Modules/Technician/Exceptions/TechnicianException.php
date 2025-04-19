<?php

namespace Modules\Technician\Exceptions;

use App\Exceptions\BaseExceptionClass;
use Symfony\Component\HttpFoundation\Response;

class TechnicianException extends BaseExceptionClass
{
    /**
     * @throws TechnicianException
     */
    public static function notFound()
    {
        throw new self(
            translate_error_message('technician', 'not_exists'),
            Response::HTTP_NOT_FOUND
        );
    }
}
