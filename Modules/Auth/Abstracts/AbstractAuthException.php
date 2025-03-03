<?php

namespace Modules\Auth\Abstracts;

use App\Exceptions\BaseExceptionClass;
use App\Exceptions\ValidationErrorsException;

abstract class AbstractAuthException extends BaseExceptionClass
{
    public static function createInstance(): static
    {
        return new static;
    }

    /**
     * @throws ValidationErrorsException
     */
    protected function throwValidationException(array $errors)
    {
        throw new ValidationErrorsException($errors);
    }
}
