<?php

namespace Modules\Map\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class MapException extends Exception
{
    public static function couldNotCalculateDistance()
    {
        throw new self(translate_word('could_not_calculate_distance'), Response::HTTP_BAD_REQUEST);
    }
}
