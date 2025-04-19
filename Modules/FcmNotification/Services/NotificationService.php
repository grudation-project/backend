<?php

namespace Modules\FcmNotification\Services;

use Modules\FcmNotification\Entities\NotificationModel;

class NotificationService
{
    public function index()
    {
        return NotificationModel::whereUserNotifiableType()
            ->whereNotifiableId(auth()->id())
            ->latest()
            ->paginatedCollection();
    }

    public function markOneAsRead(string $notification): bool|int
    {
        $notification = NotificationModel::whereUserNotifiableType()
            ->whereNotifiableId(auth()->id())
            ->whereId($notification)
            ->whereNull('read_at')
            ->firstOrFail(['id', 'read_at']);

        return $notification->update(['read_at' => now()]);
    }

    public function markAllAsRead(): bool|int
    {
        return NotificationModel::whereUserNotifiableType()
            ->whereNotifiableId(auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function deleteNotification(string $notification)
    {
        $notification = NotificationModel::whereUserNotifiableType()
            ->whereNotifiableId(auth()->id())
            ->whereId($notification)
            ->firstOrFail(['id']);

        return $notification->delete();
    }

    public function deleteAllNotifications()
    {
        return NotificationModel::whereUserNotifiableType()
            ->whereNotifiableId(auth()->id())
            ->delete();
    }
}
