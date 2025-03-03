<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Exceptions\LoginException;
use Modules\Auth\Helpers\UserTypeHelper;

class MobileTypesOnlyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! in_array(UserTypeEnum::getUserType(), UserTypeHelper::mobileTypes())) {
            LoginException::forbiddenPhoneUpdate();
        }

        return $next($request);
    }
}
