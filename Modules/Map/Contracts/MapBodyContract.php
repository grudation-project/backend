<?php

namespace Modules\Map\Contracts;

interface MapBodyContract
{
    public static function createInstance(array $parameters = []): MapBodyContract;

    public function prepare();
}
