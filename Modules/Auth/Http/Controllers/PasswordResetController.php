<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Http\Requests\PasswordResetRequest;
use Modules\Auth\Http\Requests\ValidateCodeRequest;
use Modules\Auth\Strategies\Verifiable;

class PasswordResetController extends Controller
{
    use HttpResponse;

    private Verifiable $verifiable;

    public function __construct(Verifiable $verifiable)
    {
        $this->verifiable = $verifiable;
    }

    public function forgotPassword(PasswordResetRequest $request): JsonResponse
    {
        DB::transaction(fn () => $this->verifiable->forgetPassword($request->handle));

        return $this->okResponse(message: translate_word('forgot_password_sent'));
    }

    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        DB::transaction(fn () => $this->verifiable->resetPassword(
            $request->handle,
            $request->code,
            $request->password
        ));

        return $this->okResponse(
            message: translate_word('password_reset_successfully')
        );
    }

    public function validateCode(ValidateCodeRequest $request): JsonResponse
    {
        $this->verifiable->validateCode($request->handle, $request->code);

        return $this->okResponse([
            'correct' => true,
        ]);
    }
}
