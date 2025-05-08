<?php

namespace Modules\Map\Adapters\OpenRouteService\Direction;

use Illuminate\Support\Facades\Http;
use Modules\Map\Contracts\DirectionContract;
use Modules\Map\Exceptions\CouldNotCalculateDistanceException;
use Modules\Map\Exceptions\MapException;
use Modules\Map\Helpers\PolylineHelper;

class OpenRouteServiceDirectionAdapter implements DirectionContract
{
    public function getDirection(array $coordinates, array $parameters = [])
    {
        $url = $this->getUrl();

        $additionalValidData = MapDirectionBody::createInstance()
            ->getValidData();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
        ])
            ->post($url, [
                'coordinates' => $coordinates,
                ...$additionalValidData,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new MapException($response->body());
    }

    /**
     * @throws MapException
     * @throws CouldNotCalculateDistanceException
     */
    public function findShortestPath(array $coordinates, array $parameters = []): array
    {
        $response = $this->getDirection($coordinates, $parameters);

        $summary = $response['features'][0]['properties']['summary'];

        $distance = $summary['distance'] ?? null;

        if (is_null($distance)) {
            MapException::couldNotCalculateDistance();
            throw new CouldNotCalculateDistanceException;
        }

        $encodedPolyline = PolylineHelper::encodePolyline(array_map(fn ($i) => [$i[1], $i[0]], $response['features'][0]['geometry']['coordinates']));

        return [
            'polyline' => $encodedPolyline,
            'distance' => $distance,
        ];
    }

    public function getUrl()
    {
        return config('services.map.openrouteservice.direction_url');
    }
}
