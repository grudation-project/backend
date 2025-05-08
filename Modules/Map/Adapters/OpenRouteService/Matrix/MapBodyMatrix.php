<?php

namespace Modules\Map\Adapters\OpenRouteService\Matrix;

use Modules\Map\Abstracts\AbstractMapBody;
use Modules\Map\Contracts\MapBodyContract;

class MapBodyMatrix extends AbstractMapBody implements MapBodyContract
{
    public static function createInstance(array $parameters = []): self
    {
        return new self($parameters);
    }

    private function setMetrics(): void
    {
        $validMetrics = ['distance', 'duration'];
        $metrics = $this->parameters['metrics'] ?? [];

        $this->parameters['metrics'] = is_array($metrics) && $metrics
            ? $metrics
            : ['distance'];
    }

    public function setUnit(): void
    {
        $allowedUnits = ['m', 'km', 'mi'];
        $unit = $this->parameters['units'] ?? 'm';

        $this->parameters['units'] = in_array($unit, $allowedUnits) ? $unit : 'm';
    }

    public function prepare(): void
    {
        $this->setMetrics();
        $this->setUnit();
    }

    public function getValidData(): array
    {
        $this->prepare();

        return $this->parameters;
    }
}
