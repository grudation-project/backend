<?php

namespace Modules\Auth\Exceptions;

use App\Exceptions\BaseExceptionClass;
use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginException extends BaseExceptionClass
{
    use HttpResponse;

    /**
     * @throws LoginException
     */
    public static function wrongCredentials(): self
    {
        throw new self(
            translate_word('wrong_credentials'),
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @throws LoginException
     */
    public static function rejectedApproval(): self
    {
        throw new self(
            translate_word('rejected_approval'),
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws LoginException
     */
    public static function pendingApproval()
    {
        throw new self(
            translate_word('pending_approval'),
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws LoginException
     */
    public static function notVerified(): self
    {
        throw new self(
            translate_word('account_not_verified'),
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws LoginException
     */
    public static function blockedAccount()
    {
        throw new self(
            translate_word('blocked_account'),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @throws LoginException
     */
    public static function forbiddenPhoneUpdate()
    {
        throw new self(
            translate_word('cannot_update_phone'),
            Response::HTTP_BAD_REQUEST,
        );
    }

    /**
     * @throws LoginException
     */
    public static function notApproved()
    {
        throw new self(
            translate_word('not_approved'),
            Response::HTTP_FORBIDDEN,
        );
    }
}
