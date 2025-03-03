<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Actions\ChangePassword;
use Modules\Auth\Http\Requests\ChangePasswordRequest;

class PasswordController extends Controller
{
    use HttpResponse;

    public function changePassword(
        ChangePasswordRequest $request,
        ChangePassword $changePassword
    ): JsonResponse {
        $changePassword->handle($request->validated(), auth()->id());

        return $this->okResponse(
            message: translate_success_message('password', 'changed')
        );
    }
}
