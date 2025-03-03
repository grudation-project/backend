<?php

namespace Modules\Auth\Traits;

use Modules\Auth\Helpers\UserTypeHelper;

trait AuthTrait
{
    public function whereValidType(bool $inMobile)
    {
        return $this
            ->when(! $inMobile, fn ($query) => $query->whereIn('type', UserTypeHelper::nonMobileTypes()))
            ->when($inMobile, fn ($query) => $query->whereNotIn('type', UserTypeHelper::nonMobileTypes()));
    }
}
