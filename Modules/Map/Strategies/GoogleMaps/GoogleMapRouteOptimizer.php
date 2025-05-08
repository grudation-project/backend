<?php

namespace Modules\Map\Strategies\GoogleMaps;

use Exception;
use GuzzleHttp\Client;
use Modules\Map\Abstracts\RouteOptimizer;
use Modules\Map\Exceptions\MapException;

class GoogleMapRouteOptimizer extends RouteOptimizer
{
    public function optimize(): array
    {
        $payload = $this->preparePayload();
        $client = new Client;
        $directionUrl = config('services.map.google_maps.direction_url');
        $apiKey = config('services.map.google_maps.api_key');

        try {
            $response = $client->get($directionUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'origin' => implode(',', $payload['origin']),
                    'destination' => implode(',', $payload['destination']),
                    'waypoints' => 'optimize:true|'.implode('|', array_map(fn ($item) => implode(',', $item), $payload['waypoints'])),
                    'key' => $apiKey,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

        } catch (Exception $e) {
            throw new MapException($e->getMessage(), $e->getCode());
        }

        return $this->getParsedResponse($data);
    }

    protected function getParsedResponse(array $rawResponse): array
    {
        $route = $rawResponse['routes'][0];
        $wayPointOrders = $route['waypoint_order'];
        $totalDistance = $totalDuration = 0;
        $totalArrival = 0;
        $steps = [];

        foreach ($wayPointOrders as $point) {
            $leg = $route['legs'][$point];
            $totalArrival += $totalArrival + $leg['duration']['value'];
            $steps[] = [
                'id' => $point,
                'distance' => $leg['distance']['value'],
                'duration' => $leg['duration']['value'],
                'arrival' => $totalArrival,
            ];

            $totalDistance += $leg['distance']['value'];
            $totalDuration += $leg['duration']['value'];
        }

        $lastLeg = $route['legs'][count($wayPointOrders) - 1];
        $totalDistance += $lastLeg['distance']['value'];
        $totalDuration += $lastLeg['duration']['value'];
        $lastPlaceDuration = $lastLeg['duration']['value'];

        return [
            'polyline' => $route['overview_polyline']['points'],
            'distance' => $totalDistance,
            'duration' => $totalDuration,
            'last_place_duration' => $lastPlaceDuration,
            'steps' => $steps,
        ];
    }

    protected function preparePayload(): array
    {
        return [
            'origin' => [$this->startPoint->getLat(), $this->startPoint->getLng()],
            'destination' => [$this->endPoint->getLat(), $this->endPoint->getLng()],
            'waypoints' => $this->locations->map(fn ($location) => [$location->getLat(), $location->getLng()])->toArray(),
        ];
    }
}
