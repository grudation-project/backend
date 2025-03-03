<?php

namespace Modules\Auth\Enums;

use App\Models\User;

enum UserTypeEnum
{
    const ADMIN = 0;
    const USER = 1;
    const MANAGER = 2;
    const TECHNICIAN = 3;


    public static function availableTypes(): array
    {
        return [
            self::ADMIN,
            self::USER,
            self::MANAGER,
            self::TECHNICIAN,
        ];
    }

    public static function getUserType(?User $user = null)
    {
        $user = ! is_null($user) ? $user : auth()->user();

        return $user?->type;
    }

    public static function alphaTypes(): array
    {
        return [
            self::ADMIN => 'admin',
            self::MANAGER => 'manager',
            self::USER => 'user',
            self::TECHNICIAN => 'technician',
        ];
    }
}
