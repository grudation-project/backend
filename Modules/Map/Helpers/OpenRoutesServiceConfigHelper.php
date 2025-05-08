<?php

namespace Modules\Map\Helpers;

use Modules\Map\Contracts\MapConfigContract;

class OpenRoutesServiceConfigHelper implements MapConfigContract
{
    public function baseUrl()
    {
        return config('map.openrouteservice.base_url');
    }

    public function directionsUrl()
    {
        return config('map.openrouteservice.directions_url');
    }

    public function matrixUrl()
    {
        return config('map.openrouteservice.matrix_url');
    }

    public function routingUrl()
    {
        return config('map.openrouteservice.routing_url');
    }
}
