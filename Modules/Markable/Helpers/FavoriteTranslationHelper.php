<?php

namespace Modules\Markable\Helpers;

class FavoriteTranslationHelper
{
    public static function en(): array
    {
        return [
            'model' => 'Like',
            'toggled' => 'updated successfully',
        ];
    }

    public static function ar(): array
    {
        return [
            'model' => 'الإعجاب',
            'toggled' => 'تم تحديثه بنجاح',
        ];
    }

    public static function fr(): array
    {
        return [
            'model' => 'Article',
            'toggled' => 'Basculé avec succès',
        ];
    }
}
