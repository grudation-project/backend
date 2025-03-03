<?php

namespace Modules\Auth\Http\Middleware;

use App\Traits\HttpResponse;
use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Enums\UserTypeEnum;

class CheckUserType
{
    use HttpResponse;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $types, bool $includeAdmins = false)
    {
        $types = explode('|', $types);

        if ($includeAdmins) {
            $types[] = UserTypeEnum::ADMIN;
            $types[] = UserTypeEnum::ADMIN_EMPLOYEE;
        }

        if (! auth()->check() || ! in_array(UserTypeEnum::getUserType(), $types)) {
            return $this->forbiddenResponse(translate_word('access_denied'));
        }

        return $next($request);
    }
}
