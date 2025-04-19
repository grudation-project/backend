<?php

namespace Modules\FcmNotification\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\FcmNotification\Helpers\NotificationHelper;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this->data;

        $title = $data['title'];
        $body = $data['body'];
        $shouldTranslate = $data['should_translate'] ?? [];
        $translatedAttributes = $data['translated_attributes'] ?? [];

        unset(
            $data['should_translate'],
            $data['translated_attributes'],
            $data['title'],
            $data['body'],
            $data['image'],
        );

        return [
            'id' => $this->id,
            'title' => NotificationHelper::translatedKey(
                'title',
                $title,
                auth()->user(),
                shouldTranslate: $shouldTranslate,
                translatedAttributes: $translatedAttributes
            ),
            'created_at' => $this->created_at,
            'seen' => ! is_null($this->read_at),
            'body' => NotificationHelper::translatedKey(
                'body',
                $body,
                auth()->user(),
                shouldTranslate: $shouldTranslate,
                translatedAttributes: $translatedAttributes
            ),
            'data' => $data['data'],
        ];
    }
}
