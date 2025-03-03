<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Http\Requests\CodeSendRequest;
use Modules\Auth\Http\Requests\VerifyUserRequest;
use Modules\Auth\Strategies\Verifiable;
use Throwable;

class VerifyController extends Controller
{
    use HttpResponse;

    private Verifiable $verifiable;

    public function __construct(Verifiable $verifiable)
    {
        $this->verifiable = $verifiable;
    }

    /**
     * @throws Throwable
     */
    public function send(CodeSendRequest $request): JsonResponse
    {
        $handle = $request->handle;

        DB::transaction(fn () => $this->verifiable->sendCode($handle));

        return $this->okResponse(message: translate_word('resend_verify_code'));
    }

    public function verify(VerifyUserRequest $request): JsonResponse
    {
        $handle = $request->handle;
        $code = $request->code;

        DB::transaction(fn () => $this->verifiable->verifyCode($handle, $code));

        return $this->okResponse(message: translate_word('verified'));
    }
}
