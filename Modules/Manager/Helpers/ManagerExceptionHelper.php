<?php

namespace Modules\Manager\Helpers;

use App\Helpers\BaseExceptionHelper;
use Illuminate\Foundation\Configuration\Exceptions;
use Modules\Manager\Exceptions\ManagerException;

class ManagerExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(fn(ManagerException $e) => self::generalErrorResponse($e));
    }
}
