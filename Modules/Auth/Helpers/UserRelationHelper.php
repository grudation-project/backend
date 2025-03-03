<?php

namespace Modules\Auth\Helpers;

use Modules\Auth\Enums\UserTypeEnum;

class UserRelationHelper
{
    public static function loadUserRelations($user): void
    {
        $type = $user->type;

        switch ($type) {
            case UserTypeEnum::ADMIN:
            case UserTypeEnum::ADMIN_EMPLOYEE:
                self::loadDashboardRelations($user);
                break;
        }
    }

    public static function loadDashboardRelations($user): void
    {
        $user->load('permissionsOnly.permissions');
    }
}
