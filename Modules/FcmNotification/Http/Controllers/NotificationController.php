<?php

namespace Modules\FcmNotification\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\FcmNotification\Services\NotificationService;
use Modules\FcmNotification\Transformers\NotificationResource;

class NotificationController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly NotificationService $notificationService) {}

    public function index()
    {
        $notifications = $this->notificationService->index();

        return $this->paginatedResponse($notifications, NotificationResource::class);
    }

    public function unreadNotificationsCount(): JsonResponse
    {
        return $this->resourceResponse(
            [
                'unreadNotificationsCount' => auth()->user()->unreadNotifications()->count(),
            ]
        );
    }

    public function markOneAsRead(string $notification): JsonResponse
    {
        $this->notificationService->markOneAsRead($notification);

        return $this->okResponse(
            message: translate_success_message('notification', 'read')
        );

    }

    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead();

        return $this->okResponse(
            message: translate_success_message('notifications', 'read')
        );
    }

    public function destroyOne(string $notification): JsonResponse
    {
        $this->notificationService->deleteNotification($notification);

        return $this->okResponse(
            message: translate_success_message('notification', 'deleted')
        );
    }

    public function destroyAll(): JsonResponse
    {
        $this->notificationService->deleteAllNotifications();

        return $this->okResponse(
            message: translate_success_message('notifications', 'deleted')
        );
    }

    public function updateToken()
    {
        self::baseUpdateTokenLogic();
        return $this->okResponse(message: translate_success_message('profile', 'updated'));
    }

    public static function baseUpdateTokenLogic()
    {
        $fcmToken = request()->input('fcm_token');
        $currentFcmTokens = auth()->user()->fcm_tokens;
        $currentFcmTokens[] = $fcmToken;
        $currentFcmTokens = array_unique(array_filter($currentFcmTokens));

        auth()->user()->forceFill(['fcm_tokens' => $currentFcmTokens])->save();
    }
}
