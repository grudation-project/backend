<?php

namespace App\Helpers;

use App\Http\Middleware\AccountMustBeActive;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Helpers\UserTypeHelper;
use Modules\Auth\Http\Middleware\MustBeVerified;

class GeneralHelper
{
    public static function adminMiddlewares(): array
    {
        return array_merge(self::getDefaultLoggedUserMiddlewares(['user_type_in:'.UserTypeEnum::ADMIN]));
    }

    public static function managerMiddlewares()
    {
        return array_merge(self::getDefaultLoggedUserMiddlewares(['user_type_in:'.UserTypeEnum::MANAGER]));
    }

    public static function getDefaultLoggedUserMiddlewares(array $additionalMiddlewares = []): array
    {
        return [
            self::authMiddleware(),
            MustBeVerified::class,
            AccountMustBeActive::class,
            ...$additionalMiddlewares,
        ];
    }

    public static function userTypeIn(array|string $types = [], bool $allowAdminAccess = true)
    {
        $types = is_string($types) ? $types : implode('|', $types);

        if($allowAdminAccess) {
            $types .= '|'.implode('|', UserTypeHelper::nonMobileTypes());
            $types = trim($types, '|');
        }

        return 'user_type_in:'. $types;
    }

    public static function authMiddleware(): string
    {
        return 'auth:sanctum';
    }
}
