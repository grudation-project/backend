<?php

namespace Modules\Auth\Helpers;

class VerifyConfigHelper
{
    public static function enabled(): bool
    {
        $value = config('auth.verify.enabled', true);

        return in_array($value, ['true', '1', true], true);
    }

    public static function defaultStrategy()
    {
        return config('auth.verify.strategy');
    }

    public static function getStrategyClass($strategy)
    {
        return config('auth.verify.strategies.'.$strategy.'.class');
    }
}
