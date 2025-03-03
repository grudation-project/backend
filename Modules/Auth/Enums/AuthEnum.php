<?php

namespace Modules\Auth\Enums;

use App\Models\User;

enum AuthEnum
{
    const UNIQUE_COLUMN = 'email';

    const VERIFIED_AT = 'email_verified_at';

    const AVATAR_COLLECTION_NAME = 'avatar';

    const AVATAR_RELATIONSHIP_NAME = 'avatar';

    public static function isUniqueKeyEmail(): bool
    {
        return self::UNIQUE_COLUMN == 'email';
    }

    public static function getUniqueColumn(): string
    {
        return self::UNIQUE_COLUMN;
    }

    public static function userShouldBeVerified(User $user): bool
    {
        return ! $user->{AuthEnum::VERIFIED_AT};
    }
}
