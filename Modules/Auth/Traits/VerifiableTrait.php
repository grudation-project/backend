<?php

namespace Modules\Auth\Traits;

use App\Exceptions\ValidationErrorsException;
use App\Models\Builders\UserBuilder;
use App\Models\User;
use Modules\Auth\Abstracts\AbstractVerifyUser;
use Modules\Auth\Entities\VerifyToken;
use Modules\Auth\Enums\VerifyTokenTypeEnum;
use Modules\Auth\Exceptions\VerificationCodeException;

trait VerifiableTrait
{
    private const EXPIRES_AFTER_HOURS = 2;

    public static function verificationTokenExpiryHours(): int
    {
        return self::EXPIRES_AFTER_HOURS;
    }

    /**
     * @throws ValidationErrorsException
     */
    public function generalSendCode($handle, $type = VerifyTokenTypeEnum::VERIFICATION): array
    {
        [$user, $code] = $this->prepareVerificationToken($handle);

        if ($type == VerifyTokenTypeEnum::VERIFICATION) {
            $this->validateAlreadyVerified($user);
        }

        $this->createVerificationToken($user, $code, $type);

        return [
            $user,
            $code,
        ];
    }

    /**
     * @throws ValidationErrorsException
     */
    public function getUserByHandle(User|string $handle): User
    {
        $user = $handle instanceof User
            ? $handle
            : User::query()
                ->when(true, fn(UserBuilder $b) => $b->onlyAllowedUsers())
                ->where(User::getUniqueColumnName(), $handle)
                ->first();

        if (! $user) {
            throw new ValidationErrorsException([
                'user' => translate_error_message('user', 'not_exists'),
            ]);
        }

        return $user;
    }

    protected function generateVerificationToken(string $handle, ?int $code = null, int $type = VerifyTokenTypeEnum::VERIFICATION): array
    {
        return [
            'handle' => $handle,
            'code' => $this->generateEncryptedCode($code),
            'expires_at' => now()->addHours(static::verificationTokenExpiryHours()),
            'type' => $type,
        ];
    }

    protected function updateOrCreateVerificationToken(array $data): void
    {
        VerifyToken::updateOrCreate([
            'handle' => $data['handle'],
            'type' => $data['type'],
        ], $data);
    }

    protected function generateRandomCode(): int
    {
        return rand(1000, 9999);
    }

    protected function generateEncryptedCode(?int $code = null, string $algo = 'sha256'): string
    {
        $code = is_null($code) ? $this->generateRandomCode() : $code;

        return self::encryptCode($code, $algo);
    }

    public function encryptCode(int $code, string $algo = 'sha256')
    {
        return hash($algo, $code);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function prepareVerificationToken($handle): array
    {
        $user = $this->getUserByHandle($handle);
        $code = $this->generateRandomCode();

        return [
            $user,
            $code,
        ];
    }

    public function createVerificationToken(User $user, $code, int $type = VerifyTokenTypeEnum::VERIFICATION): void
    {
        $payload = $this->generateVerificationToken(
            $user->getUniqueColumnValue(),
            $code,
            $type,
        );

        $this->updateOrCreateVerificationToken($payload);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function generalVerifyCode($handle, $code, $type = VerifyTokenTypeEnum::VERIFICATION): User
    {
        [$user] = $this->prepareVerificationToken($handle);

        if ($type == VerifyTokenTypeEnum::VERIFICATION) {
            $this->validateAlreadyVerified($user);
        }

        $verifyToken = $this->validateVerificationToken($user, $code, $type);

        if ($type == VerifyTokenTypeEnum::VERIFICATION) {
            AbstractVerifyUser::updateUserVerificationStatus($user);
        }

        $verifyToken->delete();

        return $user;
    }

    /**
     * @throws ValidationErrorsException
     */
    public function validateVerificationToken(User $user, int $code, int $type = VerifyTokenTypeEnum::VERIFICATION): VerifyToken
    {
        $verifyToken = VerifyToken::query()
            ->where('handle', $user->getUniqueColumnValue())
            ->where('code', $this->generateEncryptedCode($code))
            ->where('type', $type)
            ->first();

        if (! $verifyToken) {
            VerificationCodeException::createInstance()->invalidCode();
        }

        if (now()->isAfter($verifyToken->expires_at)) {
            VerificationCodeException::createInstance()->expiredCode();
        }

        return $verifyToken;
    }

    /**
     * @throws ValidationErrorsException
     */
    public function generalValidateCode($handle, $code, int $type = VerifyTokenTypeEnum::VERIFICATION): void
    {
        $user = $this->getUserByHandle($handle);

        $this->validateVerificationToken($user, $code, $type);
    }
}
