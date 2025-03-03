<?php

namespace Modules\Auth\Services;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use Modules\Auth\Exceptions\PhoneUpdateException;
use Modules\Auth\Models\PhoneUpdate;
use Modules\Auth\Traits\VerifiableTrait;
use Modules\Otp\Contracts\OtpContract;

readonly class PhoneUpdateService
{
    use VerifiableTrait;

    public function __construct(private OtpContract $otpContract) {}

    public function sendOtp(array $data): void
    {
        $user = auth()->user();

        if ($user->phone == $data['phone']) {
            return;
        }

        UserService::columnExists($data['phone']);

        $code = $this->generateRandomCode();

        PhoneUpdate::query()->where('user_id', $user->id)->updateOrCreate([
            'user_id' => $user->id,
        ], [
            'user_id' => $user->id,
            'code' => $this->encryptCode($code),
            'phone' => $data['phone'],
            'dial_code_length' => $data['dial_code_length'] ?? null,
            'expires_at' => now()->addHours(self::verificationTokenExpiryHours()),
        ]);

        $this->otpContract->send($data['phone'], "OTP is $code");
    }

    /**
     * @throws ValidationErrorsException
     * @throws PhoneUpdateException
     */
    public function update(array $data, ?User $user = null)
    {
        $user = $user ?: auth()->user();

        $phoneUpdateRequest = PhoneUpdate::query()->where('user_id', $user->id)->first();

        if (! $phoneUpdateRequest) {
            PhoneUpdateException::notExists();
        }

        UserService::columnExists($phoneUpdateRequest->phone, auth()->id());

        if ($phoneUpdateRequest->code !== $this->encryptCode($data['code'])) {
            PhoneUpdateException::invalidCode();
        }

        if ($phoneUpdateRequest->expires_at->isPast()) {
            PhoneUpdateException::expiredCode();
        }

        $user->forceFill([
            'phone' => $phoneUpdateRequest->phone,
            //            'dial_code_length' => $phoneUpdateRequest->dial_code_length,
        ])
            ->save();

        $phoneUpdateRequest->delete();
    }
}
