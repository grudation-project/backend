<?php

namespace Modules\Map\Helpers;

class MapTranslationHelper
{
    public static function en()
    {
        return [
            'coordinates_out_of_bounds' => 'Coordinates out of bounds',
            'coordinates' => 'Coordinates',
            'out_of_bounds' => 'Out of bounds',
            'could_not_calculate_distance' => 'Could not calculate total delivery distance, maybe coordinates are in the same place',
            'too_long_distance' => 'Too long distance, distance between places cannot be more than 100 KM',
        ];
    }

    public static function ar()
    {
        return [
            'coordinates_out_of_bounds' => 'الإحداثيات خارج النطاق',
            'coordinates' => 'الإحداثيات',
            'out_of_bounds' => 'خارج النطاق',
            'could_not_calculate_distance' => 'لا يمكن حساب المسافة الكلية للتوصيل، ربما الإحداثيات في نفس المكان',
            'too_long_distance' => 'مسافة طويلة جدا، المسافة بين الأماكن لا يمكن أن تكون أكثر من 100 كم',
        ];
    }

    public static function fr()
    {
        return [
            'coordinates_out_of_bounds' => 'Coordonnées hors limites',
            'coordinates' => 'Coordonnées',
            'out_of_bounds' => 'Hors limites',
            'could_not_calculate_distance' => 'Impossible de calculer la distance totale de livraison, peut-être que les coordonnées sont au même endroit',
        ];
    }

    public static function ku()
    {
        return [
            'coordinates_out_of_bounds' => 'پلان خارا لە دەق',
            'coordinates' => 'پلان',
            'out_of_bounds' => 'لە دەق',
            'could_not_calculate_distance' => 'نەتوانرا دووربینی گشتیی پێداچوو، شاید پلانەکان لە هاوسازی هەیە',
            'too_long_distance' => 'دووربینی زۆر درێژ، دووربینی لە نێوان شوێنەکان ناتوانێت زیاتر لە 100 کیلۆمەتر بێت',
        ];
    }
}
