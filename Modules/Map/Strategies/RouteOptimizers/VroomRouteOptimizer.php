<?php

namespace Modules\Map\Strategies\RouteOptimizers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Map\Abstracts\RouteOptimizer;
use Modules\Map\Exceptions\MapException;
use Modules\Map\Helpers\PointHelper;

class VroomRouteOptimizer extends RouteOptimizer
{
    /**
     * @throws GuzzleException
     * @throws MapException
     */
    public function optimize(): array
    {
        $payload = $this->preparePayload();
        $client = new Client;

        try {
            $response = $client->post(env('MAP_OPTIMIZATION_ENDPOINT'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($payload),
            ]);

            $data = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new MapException($e->getMessage(), $e->getCode());
        }

        return $this->getParsedResponse($data);
    }

    public function preparePayload(): array
    {
        $jobs = [];

        foreach ($this->locations as $index => $location) {
            $jobs[] = [
                'id' => $index,
                'service' => 60,
                'amount' => [1],
                'location' => PointHelper::destructLatLngArray($location),
            ];
        }

        return [
            'jobs' => $jobs,
            'vehicles' => [
                [
                    'id' => 1,
                    'profile' => 'driving-car',
                    'start' => PointHelper::destructLatLngArray($this->startPoint),
                    'end' => PointHelper::destructLatLngArray($this->endPoint),
                    'capacity' => [$this->busCapacity],
                    'skills' => [1],
                ],
            ],
        ];
    }

    protected function getParsedResponse(array $rawResponse): array
    {
        $steps = collect($rawResponse['routes'][0]['steps'])
            ->filter(fn ($step) => $step['type'] == 'job')
            ->select(['id', 'distance', 'duration', 'arrival']);

        return [
            'polyline' => $rawResponse['routes'][0]['geometry'],
            'unassigned_count' => $rawResponse['summary']['unassigned'],
            'distance' => $rawResponse['summary']['distance'],
            'duration' => $rawResponse['summary']['duration'],
            'last_place_duration' => 0,
            'steps' => $steps->values(),
        ];
    }
}
