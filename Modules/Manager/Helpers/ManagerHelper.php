<?php

namespace Modules\Manager\Helpers;

use Modules\Manager\Exceptions\ManagerException;

class ManagerHelper
{
    public static function getUserManager($user = null)
    {
        $user = $user ?: auth()->user();
        $manager = $user->manager;

        if(! $manager) {
            ManagerException::notExists();
        }

        return $manager;
    }
}
