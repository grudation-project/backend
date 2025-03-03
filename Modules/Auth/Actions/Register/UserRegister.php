<?php

namespace Modules\Auth\Actions\Register;

use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Strategies\Verifiable;

class UserRegister
{
    public function handle(array $data, bool $byAdmin = false)
    {
        $data['type'] = UserTypeEnum::USER;

        return (new BaseRegisterAction)->handle($data, app(Verifiable::class), byAdmin: $byAdmin);
    }
}
