<?php

namespace Modules\FcmNotification\Entities;

use App\Models\User;
use App\Traits\PaginationTrait;
use Illuminate\Notifications\DatabaseNotification;

class NotificationModel extends DatabaseNotification
{
    use PaginationTrait;

    public function scopeWhereUserNotifiableType($query)
    {
        return $query->whereNotifiableType(User::class);
    }
}
