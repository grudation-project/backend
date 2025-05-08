<?php

namespace Modules\Map\Helpers;

use App\Helpers\BaseExceptionHelper;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Str;
use Modules\Map\Exceptions\MapException;
use Symfony\Component\HttpFoundation\Response;

class MapExceptionHelper extends BaseExceptionHelper
{
    public static function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(function (MapException $e) {
            $message = $e->getMessage();
            $message = json_decode($message, true)['error']['message'] ?? $message;

            if (Str::contains($message, 'Invalid jobs or shipments')) {
                $message = 'invalid or empty coordinates';
            }

            if (Str::contains($message, 'out of bounds')) {
                $message = translate_word('coordinates_out_of_bounds');
            }

            if (Str::contains($message, 'The approximated route distance must not be greater than')) {
                $message = translate_word('too_long_distance');
            }

            return self::generalErrorResponse($e, code: Response::HTTP_BAD_REQUEST, message: $message);
        });
    }
}
