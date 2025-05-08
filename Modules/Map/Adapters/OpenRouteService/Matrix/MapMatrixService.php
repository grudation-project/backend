<?php

namespace Modules\Map\Adapters\OpenRouteService\Matrix;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Modules\Map\Contracts\MatrixContract;
use Modules\Map\Exceptions\MapException;

class MapMatrixService implements MatrixContract
{
    private ?string $matrixUrl = null;

    public static function create(): MapMatrixService
    {
        return new self;
    }

    /**
     * @throws MapException
     * @throws ConnectionException
     */
    public function getMatrix(array $coordinates, array $data = [])
    {
        $coordinates = $this->prepareGeometry($coordinates);

        $url = $this->getUrl();

        $matrixAdditionalData = MapBodyMatrix::createInstance($data)
            ->getValidData();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
        ])->post($url, [
            'locations' => $coordinates,
            ...$matrixAdditionalData,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new MapException($response->body(), $response->status());
    }

    public function getSortedCoordinates(array $coordinates, array $additionalData = []): array
    {
        $matrixResponse = $this->getMatrix($coordinates, $additionalData);

        if ($matrixResponse) {
            $locations = $matrixResponse['metadata']['query']['locations'];
            $distances = $matrixResponse['distances'];

            for ($i = 0; $i < count($distances); $i++) {
                $distances[$i] = [
                    'id' => $i,
                    'sub_distance' => $distances[$i],
                ];
            }

            $this->sortCoordinatesDesc($distances);

            $sortedLocations = [];

            foreach ($distances as $distance) {
                $sortedLocations[] = $locations[$distance['id']];
            }

            return [
                $sortedLocations,
                $distances,
            ];
        }

        throw new Exception('Matrix Is Empty');
    }

    public function setUrl(?string $url = null): self
    {
        $this->matrixUrl = $url ?: config('services.map.openrouteservice.matrix_url');

        return $this;
    }

    public function getUrl(): string
    {
        if (! isset($this->matrixUrl)) {
            $this->setUrl();
        }

        return $this->matrixUrl;
    }

    private function sortCoordinatesDesc(array &$distances): void
    {
        usort($distances, function ($a, $b) {
            $maxLastA = end($a['sub_distance']);
            $maxLastB = end($b['sub_distance']);

            return $maxLastB <=> $maxLastA;
        });
    }

    /**
     * @throws MapException
     * @throws ConnectionException
     * @throws Exception
     */
    public function getCoordinates(array $coordinates, array $additionalData = []): array
    {
        $matrixResponse = $this->getMatrix($coordinates, $additionalData);

        if ($matrixResponse) {
            return $matrixResponse['distances'];
        }

        throw new Exception('Matrix Is Empty');
    }

    public function prepareGeometry(array $coordinates)
    {
        $geometry = [];

        foreach ($coordinates as $coordinate) {
            $geometry[] = [
                $coordinate->getLng(),
                $coordinate->getLat(),
            ];
        }

        return $geometry;
    }
}
