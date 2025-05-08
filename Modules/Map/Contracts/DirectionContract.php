<?php

namespace Modules\Map\Contracts;

interface DirectionContract
{
    public function findShortestPath(array $coordinates, array $parameters = []);
}
