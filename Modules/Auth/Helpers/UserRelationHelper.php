<?php

namespace Modules\Auth\Helpers;

use Modules\Auth\Enums\UserTypeEnum;

class UserRelationHelper
{
    public static function loadUserRelations($user): void
    {
        $type = $user->type;

        switch ($type) {
            case UserTypeEnum::MANAGER:
                $user->load('manager:id,user_id,service_id');
                break;
        }
    }

    public static function loadDashboardRelations($user): void
    {
        $user->load('permissionsOnly.permissions');
    }
}
