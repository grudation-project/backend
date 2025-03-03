<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Exceptions\SanctumTokenException;
use Modules\Auth\Http\Requests\RefreshTokenRequest;
use Modules\Auth\Models\RefreshToken;
use Modules\Auth\Services\LoginService;
use Modules\Auth\Services\RefreshTokenService;
use Modules\Auth\Transformers\SanctumTokenResource;

class AuthController extends Controller
{
    use HttpResponse;

    public function refreshToken(RefreshTokenRequest $request, RefreshTokenService $refreshTokenService): JsonResponse
    {
        $refreshToken = RefreshToken::query()
            ->where('token', $refreshTokenService->getEncryptedToken($request->token))
            ->firstOrFail();

        $refreshTokenService->assertExpired($refreshToken);
        $user = $refreshToken->user;

        return $this->resourceResponse(SanctumTokenResource::make(LoginService::generateBearerToken($user)));
    }

}
