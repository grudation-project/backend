<?php

namespace Modules\FcmNotification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\FcmNotification\Helpers\NotificationHelper;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotificationResource;

class FcmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $shouldTranslate;

    private array $translatedAttributes;

    private string $title;

    private string $body;

    private ?string $image;

    private array $additionalData;

    public function __construct(
        string  $title,
        string  $body,
        ?string $image = null,
        array   $additionalData = [],
        array   $shouldTranslate = [],
        array   $translatedAttributes = [],
        private array $viaChannels = ['database', FcmChannel::class]
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
        $this->additionalData = $additionalData;
        $this->shouldTranslate = $shouldTranslate;
        $this->translatedAttributes = $translatedAttributes;
    }

    public function via($notifiable)
    {
        return $this->viaChannels;
    }

    public function toFcm($notifiable)
    {
        $data = $this->additionalData;

        foreach(array_keys($data) as $key) {
            $data[$key] = $data[$key].'';
        }

        return (new FcmMessage(notification: new FcmNotificationResource(
             title: NotificationHelper::translatedKey('title', $this->title, $notifiable, shouldTranslate: $this->shouldTranslate, translatedAttributes: $this->translatedAttributes),
             body: NotificationHelper::translatedKey('body', $this->body, $notifiable, shouldTranslate: $this->shouldTranslate, translatedAttributes: $this->translatedAttributes),
        )
        ))
            ->data(
                $data
                + [
                    'id' => $this->id,
                ]
            );
    }

    public function toDatabase(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'image' => $this->image,
            'data' => $this->additionalData,
            'should_translate' => $this->shouldTranslate,
            'translated_attributes' => $this->translatedAttributes,
        ];
    }
}
