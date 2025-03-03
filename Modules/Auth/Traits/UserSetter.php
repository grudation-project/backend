<?php

namespace Modules\Auth\Traits;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait UserSetter
{
    protected ?User $user = null;

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User|Authenticatable
    {
        return $this->user ?: auth()->user();
    }
}
