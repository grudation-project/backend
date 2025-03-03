<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\RefreshTokenRequest;
use Modules\Auth\Services\RefreshTokenService;
use Modules\Auth\Transformers\SanctumTokenResource;

class RefreshTokenController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly RefreshTokenService $refreshTokenService) {}

    public function rotate(RefreshTokenRequest $request)
    {
        $token = $this->refreshTokenService->rotate($request->validated());

        return $this->okResponse(SanctumTokenResource::make($token));
    }
}
