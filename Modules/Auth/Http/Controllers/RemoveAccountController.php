<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Http\Requests\DeleteUserRequest;

class RemoveAccountController extends Controller
{
    use HttpResponse;

    public function __invoke(DeleteUserRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (! in_array(UserTypeEnum::getUserType(), [UserTypeEnum::ADMIN, UserTypeEnum::ADMIN_EMPLOYEE])) {
            $user->delete();

            return $this->okResponse(
                message: translate_word('account_deleted')
            );
        }

        return $this->notFoundResponse(
            message: translate_error_message('user', 'not_found')
        );
    }
}
