<?php

namespace Modules\Map\Helpers;

use Elattar\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;

class PointHelper
{
    public static function getFormattedLatLng(?Point $locationValue): ?array
    {
        if (! $locationValue) {
            return null;
        }

        return [
            'latitude' => $locationValue->getLat(),
            'longitude' => $locationValue->getLng(),
        ];
    }

    public static function mergeLocation($thisValue, string $fieldName = 'location')
    {
        return $thisValue->mergeWhen((bool) is_null($thisValue->{$fieldName}), function () use ($thisValue) {
            return self::getFormattedLatLng($thisValue->{$fieldName});
        });
    }

    public static function replaceLatLngWithLocation(array &$data, string $locationColumn = 'location', string $latitudeColumn = 'latitude', string $longitudeColumn = 'longitude')
    {
        if (isset($data[$latitudeColumn]) && $data[$longitudeColumn]) {
            $data[$locationColumn] = self::rawPoint($data[$latitudeColumn], $data[$longitudeColumn]);
            unset($data[$latitudeColumn], $data[$longitudeColumn]);
        }
    }

    public static function rawPoint($latitude, $longitude)
    {
        return DB::raw("POINT($longitude, $latitude)");
    }

    public static function destructPoint(array $data): Point
    {
        return new Point($data['latitude'], $data['longitude']);
    }

    public static function destructLatLngArray(Point $point): array
    {
        return [$point->getLng(), $point->getLat()];
    }

    public static function decodeGeomtry($encodedString, $precision = 5)
    {
        $index = 0;
        $lat = 0;
        $lng = 0;
        $coordinates = [];
        $factor = pow(10, $precision);

        while ($index < strlen($encodedString)) {
            $shift = 0;
            $result = 0;
            $byte = 0;

            do {
                $byte = ord($encodedString[$index++]) - 63;
                $result |= ($byte & 0x1F) << $shift;
                $shift += 5;
            } while ($byte >= 0x20);

            $latChange = ($result & 1) ? ~(($result >> 1) + 1) : ($result >> 1);
            $lat += $latChange;

            $shift = 0;
            $result = 0;

            do {
                $byte = ord($encodedString[$index++]) - 63;
                $result |= ($byte & 0x1F) << $shift;
                $shift += 5;
            } while ($byte >= 0x20);

            $lngChange = ($result & 1) ? ~(($result >> 1) + 1) : ($result >> 1);
            $lng += $lngChange;

            $coordinates[] = [($lat / $factor), ($lng / $factor)];
        }

        return $coordinates;
    }

    public static function getOrderedLatLng(Point $point): array
    {
        return [$point->getLng(), $point->getLat()];
    }

    public static function preparePointForQuery(mixed $point)
    {
        if ($point instanceof Point) {
            return self::rawPoint($point->getLat(), $point->getLng());
        }

        return $point;
    }
}
