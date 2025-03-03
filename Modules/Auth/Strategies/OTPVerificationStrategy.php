<?php

namespace Modules\Auth\Strategies;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use Modules\Auth\Abstracts\AbstractVerifyUser;
use Modules\Auth\Enums\VerifyTokenTypeEnum;
use Modules\Auth\Traits\VerifiableTrait;
use Modules\Otp\Contracts\OtpContract;

class OTPVerificationStrategy extends AbstractVerifyUser implements Verifiable
{
    use VerifiableTrait;

    private OtpContract $otpContract;

    public function __construct(OtpContract $otpContract)
    {
        $this->otpContract = $otpContract;
    }

    /**
     * @throws ValidationErrorsException
     */
    public function verifyCode($handle, $code): bool
    {
        $this->generalVerifyCode($handle, $code);

        return true;
    }

    /**
     * @throws ValidationErrorsException
     */
    public function sendCode($handle): bool
    {
        [$user, $code] = $this->generalSendCode($handle);

        $this->otpContract->send(
            $user->getUniqueColumnValue(),
            $this->generateVerifyMessage($code)
        );

        return true;
    }

    private function generateVerifyMessage($code): string
    {
        return translate_word('otp_verify_code', ['code' => $code]);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function forgetPassword($handle): void
    {
        [$user, $code] = $this->generalSendCode($handle, VerifyTokenTypeEnum::PASSWORD_RESET);

        $this->otpContract->send($user->phone, $this->generateVerifyMessage($code));
    }

    /**
     * @throws ValidationErrorsException
     */
    public function resetPassword($handle, $code, $newPassword): void
    {
        $user = $this->generalVerifyCode($handle, $code, VerifyTokenTypeEnum::PASSWORD_RESET);

        $user->forceFill(['password' => $newPassword])->save();
    }

    /**
     * @throws ValidationErrorsException
     */
    public function validateCode($handle, $code): void
    {
        $this->generalValidateCode($handle, $code, VerifyTokenTypeEnum::PASSWORD_RESET);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function sendPhoneCode(string $newPhone, ?User $user = null)
    {
        $user = $user ?: auth()->user();

        //TODO update code
        [$user, $code] = $this->generalSendCode($user, VerifyTokenTypeEnum::UPDATE_PHONE_NUMBER);

        $this->otpContract->send($user->phone, $this->generateVerifyMessage($code));
    }

    /**
     * @throws ValidationErrorsException
     */
    public function updatePhone($code, array $data = [], ?User $user = null)
    {
        $user = $user ?: auth()->user();

        $this->generalVerifyCode($user, $code, VerifyTokenTypeEnum::UPDATE_PHONE_NUMBER);
        $user->forceFill($data)->save();
    }
}
