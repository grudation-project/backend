<?php

namespace Modules\Auth\Actions;

use App\Models\User;

class ChangePassword
{
    public function handle(array $data, $user): bool
    {
        $user = User::where('id', $user)->first();
        $user->update([
            'password' => $data['new_password'],
        ]);

        return true;
    }
}
