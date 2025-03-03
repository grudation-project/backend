<?php

namespace Modules\Auth\Traits;

use Modules\Auth\Enums\AuthEnum;

trait HasVerifyTokens
{
    public function getUniqueColumnValue()
    {
        return $this->{static::getUniqueColumnName()};
    }

    public static function getUniqueColumnName(): string
    {
        return AuthEnum::getUniqueColumn();
    }
}
