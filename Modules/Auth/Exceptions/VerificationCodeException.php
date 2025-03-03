<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\ValidationErrorsException;
use Modules\Auth\Abstracts\AbstractAuthException;

class VerificationCodeException extends AbstractAuthException
{
    /**
     * @throws ValidationErrorsException
     */
    public function invalidCode(): self
    {
        $this->throwValidationException([
            'code' => translate_word('invalid_verify_code'),
        ]);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function expiredCode(): self
    {
        $this->throwValidationException([
            'code' => translate_word('expired_verify_code'),
        ]);
    }
}
