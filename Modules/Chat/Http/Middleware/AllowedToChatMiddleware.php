<?php

namespace Modules\Chat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowedToChatMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
