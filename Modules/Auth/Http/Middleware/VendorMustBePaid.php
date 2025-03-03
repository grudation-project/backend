<?php

namespace Modules\Auth\Http\Middleware;

use App\Traits\HttpResponse;
use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Vendor\Helpers\VendorHelper;

class VendorMustBePaid
{
    use HttpResponse;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && UserTypeEnum::getUserType() == UserTypeEnum::VENDOR) {
            $vendor = VendorHelper::getUserVendor();

            if (! $vendor->fees_paid) {

            }
        }

        return $next($request);
    }
}
