<?php

namespace Modules\Auth\Abstracts;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use Modules\Auth\Enums\AuthEnum;

abstract class AbstractVerifyUser
{
    private function userShouldBeVerified(User $user): bool
    {
        return AuthEnum::userShouldBeVerified($user);
    }

    /**
     * @throws ValidationErrorsException
     */
    protected function validateAlreadyVerified(User $user): void
    {
        if (! $this->userShouldBeVerified($user)) {
            throw new ValidationErrorsException([
                'user' => translate_word('user_already_verified'),
            ]);
        }
    }

    public static function updateUserVerificationStatus(User $user, bool $enableUser = true): void
    {
        $user->forceFill([
            AuthEnum::VERIFIED_AT => now(),
            'status' => $enableUser,
        ])
            ->save();
    }
}
