<?php

namespace Modules\FcmNotification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotificationResource;

class RealtimeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $title;

    private string $body;

    private ?string $image;

    public function __construct(
        string $title,
        string $body,
        ?string $image = null,
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return (new FcmMessage(notification: new FcmNotificationResource(
            title: $this->title,
            body: $this->body,
        )
        ))
            ->data(array_filter([
                'id' => $this->id,
                'title' => $this->title,
                'body' => $this->body,
                'image' => $this->image,
                'type' => NotificationTypeEnum::SYSTEM_NOTIFICATION,
            ])
            );
    }
}
