<?php

namespace Modules\Chat\Helpers;

use Modules\Auth\Enums\UserTypeEnum;

class ChatUserHelper
{
    public static function getTable()
    {
        return 'users';
    }

    public static function allowedToChat(?int $type = null): bool
    {
        $type = is_null($type) ? UserTypeEnum::getUserType() : $type;

        return in_array($type, self::allowedTypes());
    }

    public static function allowedTypes(): array
    {
        return [
            UserTypeEnum::NORMAL_USER,
            UserTypeEnum::VENDOR,
            UserTypeEnum::MAINTENANCE,
            UserTypeEnum::DELIVERY_MAN,
            UserTypeEnum::ADMIN,
        ];
    }
}
