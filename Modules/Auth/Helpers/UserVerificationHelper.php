<?php

namespace Modules\Auth\Helpers;

class UserVerificationHelper
{
    public static function allowedUsersTypes(): array
    {
        return UserTypeHelper::mobileTypes();
    }
}
