<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\Login\LoginRequest;
use Modules\Auth\Services\LoginService;
use Modules\Auth\Transformers\RefreshTokenResource;
use Modules\Auth\Transformers\SanctumTokenResource;
use Modules\Auth\Transformers\UserResource;

class LoginController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly LoginService $loginService) {}

    public function __invoke(LoginRequest $request)
    {
        $user = $this->loginService->login($request->validated());

        return $this->loginResponse($user);
    }

    public function loginResponse($user)
    {
        return $this->okResponse(array_merge(
            [
                'user' => UserResource::make($user),
            ],
            SanctumTokenResource::make($user->tokens['token'])->toArray(request()),
            RefreshTokenResource::make($user->tokens['refresh_token'])->toArray(request())
        ), message: translate_word('logged_in'));
    }
}
