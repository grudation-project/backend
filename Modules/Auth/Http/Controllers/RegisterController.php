<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Actions\Register\UserRegister;
use Modules\Auth\Http\Requests\Register\UserRegisterRequest;

class RegisterController extends Controller
{
    use HttpResponse;

    public function user(UserRegisterRequest $request, UserRegister $userRegister): JsonResponse
    {
        $userRegister->handle($request->validated());

        return $this->createdResponse(
            message: translate_success_message('user', 'created')
            .' '.translate_word('user_verification_sent')
        );
    }
}
