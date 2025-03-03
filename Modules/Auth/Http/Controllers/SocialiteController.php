<?php

namespace Modules\Auth\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Http\Requests\SocialAuthRequest;
use Modules\Auth\Services\SocialiteService;
use Modules\Auth\Transformers\UserResource;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class SocialiteController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly SocialiteService $socialiteService) {}

    /**
     * @throws FileCannotBeAdded
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function parent(SocialAuthRequest $request)
    {
        $user = $this->socialiteService->handleProviderCallback($request->validated(), UserTypeEnum::PARENT);

        return $this->okResponse(UserResource::make($user), message: translate_word('logged_in'));
    }

    /**
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig|\Modules\Auth\Exceptions\SocialAuthException
     */
    public function user(SocialAuthRequest $request)
    {
        $user = $this->socialiteService->handleProviderCallback($request->validated(), UserTypeEnum::USER);

        return $this->okResponse(UserResource::make($user), message: translate_word('logged_in'));
    }
}
