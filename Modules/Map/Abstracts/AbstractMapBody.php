<?php

namespace Modules\Map\Abstracts;

abstract class AbstractMapBody
{
    protected array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getValidData(): array
    {
        return $this->parameters;
    }
}
