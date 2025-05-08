<?php

namespace Modules\Map\Contracts;

interface MapConfigContract
{
    public function baseUrl();

    public function directionsUrl();

    public function matrixUrl();

    public function routingUrl();
}
