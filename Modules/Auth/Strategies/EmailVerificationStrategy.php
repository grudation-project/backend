<?php

namespace Modules\Auth\Strategies;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Abstracts\AbstractVerifyUser;
use Modules\Auth\Emails\VerifyUserEmail;
use Modules\Auth\Enums\VerifyTokenTypeEnum;
use Modules\Auth\Traits\VerifiableTrait;

class EmailVerificationStrategy extends AbstractVerifyUser implements Verifiable
{
    use VerifiableTrait;

    /**
     * @throws ValidationErrorsException
     */
    public function verifyCode($handle, $code): void
    {
        $this->generalVerifyCode($handle, $code);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function sendCode($handle): void
    {
        [$user, $code] = $this->generalSendCode($handle);

        Mail::to($user->getUniqueColumnValue(), $user->name)->send(new VerifyUserEmail([
            'code' => $code,
            'expire_after' => self::verificationTokenExpiryHours(),
        ]));
    }

    /**
     * @throws ValidationErrorsException
     */
    public function forgetPassword($handle): void
    {
        [$user, $code] = $this->generalSendCode($handle, VerifyTokenTypeEnum::PASSWORD_RESET);

        Mail::to($user->getUniqueColumnValue(), $user->name)->send(new VerifyUserEmail([
            'code' => $code,
            'expire_after' => self::verificationTokenExpiryHours(),
        ], 'auth::forgot-password', 'Reset Password'));
    }

    /**
     * @throws ValidationErrorsException
     */
    public function resetPassword($handle, $code, $newPassword): void
    {
        $user = $this->generalVerifyCode($handle, $code, VerifyTokenTypeEnum::PASSWORD_RESET);

        $user->forceFill([
            'password' => $newPassword,
        ])->save();
    }

    public function validateCode($handle, $code)
    {
        $this->generalValidateCode($handle, $code, VerifyTokenTypeEnum::PASSWORD_RESET);
    }
}
