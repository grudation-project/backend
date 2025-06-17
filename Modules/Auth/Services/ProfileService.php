<?php

namespace Modules\Auth\Services;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use App\Services\ImageService;

class ProfileService
{
    /**
     * @return true|array
     *
     * @throws ValidationErrorsException
     */
    public function handle(array $data): bool|array
    {
        $user = auth()->user();

        if (isset($data['email'])) {
            UserService::columnExists($data['email'], auth()->id(), 'email', 'email');
            UserService::assertValidCollegeEmail($data['email']);
        }

        if (isset($data['phone'])) {
            UserService::columnExists($data['phone'], auth()->id());
        }

        $user->update($data);

        if (isset($data['avatar'])) {
            (new ImageService($user, $data))->updateOneMedia(
                'avatar',
                'avatar',
                'resetAvatar',
            );
        }

        return true;
    }
}
