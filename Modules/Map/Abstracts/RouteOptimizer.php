<?php

namespace Modules\Map\Abstracts;

use Elattar\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Collection;

abstract class RouteOptimizer
{
    protected Point $startPoint;

    protected Point $endPoint;

    protected Collection $locations;

    protected int $busCapacity = 0;

    abstract public function optimize(): array;

    abstract protected function preparePayload(): array;

    abstract protected function getParsedResponse(array $rawResponse): array;

    public function setStartPoint(Point $startPoint): static
    {
        $this->startPoint = $startPoint;

        return $this;
    }

    public function setEndPoint(Point $endPoint): static
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    public function setJobs(Collection $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    public function setCarCapacity($value): static
    {
        $this->busCapacity = $value;

        return $this;
    }
}
