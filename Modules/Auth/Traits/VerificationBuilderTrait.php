<?php

namespace Modules\Auth\Traits;

use Modules\Auth\Helpers\UserVerificationHelper;

trait VerificationBuilderTrait
{
    public function onlyAllowedUsers(): static
    {
        return $this->whereIn('type', UserVerificationHelper::allowedUsersTypes());
    }
}
