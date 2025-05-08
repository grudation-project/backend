<?php

namespace Modules\Map\Contracts;

interface MatrixContract
{
    public function getCoordinates(array $coordinates, array $additionalData = []): array;
}
