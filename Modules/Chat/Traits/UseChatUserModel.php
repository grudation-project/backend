<?php

namespace Modules\Chat\Traits;

use App\Models\User;

trait UseChatUserModel
{
    public function getChatUserModel(): User
    {
        return new User;
    }

    public function getUserClass(): string
    {
        return User::class;
    }
}
