<?php

namespace Modules\FcmNotification\Helpers;

use App\Helpers\TranslationHelper;
use Illuminate\Database\Eloquent\Model;
use Modules\FcmNotification\Entities\NotificationModel;

class NotificationHelper
{
    public static function translatedKey(
        string $key,
        string $value,
        $notifiable,
        ?string $translatedKeyName = null,
        ?array $shouldTranslate = null,
        ?array $translatedAttributes = null,
    ): string {
        $translatedKeyName = $translatedKeyName ?: $value;

        return isset($shouldTranslate[$key]) && $shouldTranslate[$key]
            ?
            __(
                "messages.$translatedKeyName",
                $translatedAttributes[$translatedKeyName] ?? [],
                $notifiable->locale ?: TranslationHelper::$defaultLocale
            )
//            translate_word(
//                $translatedKeyName,
//                $translatedAttributes[$translatedKeyName] ?? [],
//            );
            : $value;
    }

    public static function deleteAllModelNotifications(Model $model): void
    {
        NotificationModel::where('notifiable_id', $model->getKey())
            ->where('notifiable_type', get_class($model))
            ->delete();
    }
}
