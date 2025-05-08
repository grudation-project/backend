<?php

namespace Modules\Map\Adapters\OpenRouteService\Direction;

use Modules\Map\Abstracts\AbstractMapBody;
use Modules\Map\Contracts\MapBodyContract;

class MapDirectionBody extends AbstractMapBody implements MapBodyContract
{
    public function prepare() {}

    public static function createInstance(array $parameters = []): MapBodyContract
    {
        return new self($parameters);
    }

    public function getValidData(): array
    {
        return [
            'profile' => 'driving-car',
            'preference' => 'shortest',
            'instructions' => 'false',
            'geometry' => 'true',
            'geometry_simplify' => 'false',
            'units' => 'm',
            'elevation' => 'false',
        ];
    }
}
