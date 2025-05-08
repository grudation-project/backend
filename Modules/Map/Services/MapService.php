<?php

namespace Modules\Map\Services;

use Elattar\LaravelMysqlSpatial\Types\Point;
use Modules\Map\Contracts\MatrixContract;

class MapService
{
    public function getSortedCoordinates($vendorsCoordinates, Point $clientCoordinates, MatrixContract $matrixContract)
    {
        $vendorsCoordinates = array_merge($vendorsCoordinates, [$clientCoordinates]);

        [$sortedCoordinates] = $matrixContract->getSortedCoordinates($vendorsCoordinates);

        return $sortedCoordinates;
    }
}
