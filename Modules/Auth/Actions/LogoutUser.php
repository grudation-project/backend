<?php

namespace Modules\Auth\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Auth\Models\SessionModel;

class LogoutUser
{
    public function handle(?Authenticatable $user = null): bool
    {
        DB::transaction(function () use ($user) {
            if (Schema::hasTable('sessions') && config('session.driver') === 'database') {
                SessionModel::query()
                    ->where('user_id', auth()->id())
                    ->orWhere('ip_address', request()->ip())
                    ->delete();
            }

            $user = $user ?: auth()->user();
            $user->tokens()->delete();

            if ($user->fcm_token) {
                $user->forceFill(['fcm_token' => null])->save();
            }

            session()->regenerateToken();
        });

        return true;
    }
}
