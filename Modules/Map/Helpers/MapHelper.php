<?php

namespace Modules\Map\Helpers;

use Elattar\LaravelMysqlSpatial\Types\LineString;
use Elattar\LaravelMysqlSpatial\Types\Point;
use Elattar\LaravelMysqlSpatial\Types\Polygon;

class MapHelper
{
    public static function generateBoundsPolyline(array $bounds)
    {
        $northEastLat = $bounds['bounds_north_east_latitude'];
        $northEastLng = $bounds['bounds_north_east_longitude'];
        $southWestLat = $bounds['bounds_south_west_latitude'];
        $southWestLng = $bounds['bounds_south_west_longitude'];

        // Create the polygon representing the rectangular bounds
        return new Polygon([
            new LineString([
                new Point($southWestLat, $southWestLng),
                new Point($southWestLat, $northEastLng),
                new Point($northEastLat, $northEastLng),
                new Point($northEastLat, $southWestLng),
                new Point($southWestLat, $southWestLng),
            ]),
        ]);
    }

    public static function roundDistance(float|int|null $distance): float|int|null
    {
        if (is_null($distance)) {
            return null;
        }

        return round($distance, 2);
    }

    public static function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius of Earth in meters

        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Calculate the difference in coordinates
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        // Apply the Haversine formula
        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Calculate the distance
        $distance = $earthRadius * $c;

        return $distance;
    }
}
