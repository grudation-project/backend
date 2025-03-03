<?php

namespace Modules\Auth\Helpers;

use App\Helpers\BaseExceptionHelper;
use App\Traits\HttpResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Modules\Auth\Enums\ForbiddenLoginEnum;
use Modules\Auth\Exceptions\LoginException;
use Modules\Auth\Exceptions\RefreshTokenException;
use Modules\Auth\Exceptions\SanctumTokenException;
use Modules\Auth\Exceptions\UserException;
use Modules\Auth\Exceptions\VerificationCodeException;

class AuthExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(function (LoginException $e) {
            $message = $e->getMessage();

            return match ($message) {
                translate_word('pending_approval') => self::generalErrorResponse($e, additional: [
                    'forbidding_reason' => ForbiddenLoginEnum::PENDING_APPROVAL,
                ]),
                translate_word('rejected_approval') => self::generalErrorResponse($e, additional: [
                    'forbidding_reason' => ForbiddenLoginEnum::REJECTED_APPROVAL,
                ]),
                translate_word('account_not_verified') => self::generalErrorResponse($e, additional: [
                    'forbidding_reason' => ForbiddenLoginEnum::NOT_VERIFIED,
                ]),
                translate_word('not_approved') => self::generalErrorResponse($e, additional: [
                    'forbidding_reason' => ForbiddenLoginEnum::NOT_APPROVED,
                ]),
                translate_word('blocked_account') => self::generalErrorResponse($e, additional: [
                    'forbidding_reason' => ForbiddenLoginEnum::ACCOUNT_SUSPENDED,
                ]),

                default => self::generalErrorResponse($e),
            };
        });

        $exceptions->renderable(fn (VerificationCodeException $e) => self::generalErrorResponse($e));
        $exceptions->renderable(fn (SanctumTokenException $e) => self::generalErrorResponse($e));
        $exceptions->renderable(fn (RefreshTokenException $e) => self::generalErrorResponse($e));
        $exceptions->renderable(fn (UserException $e) => self::generalErrorResponse($e));
        $exceptions->renderable(function (AuthenticationException $e, $req) {
            $httpResponse = new class
            {
                use HttpResponse;
            };

            return $httpResponse->unauthenticatedResponse('You are not authenticated');
        });
    }
}
