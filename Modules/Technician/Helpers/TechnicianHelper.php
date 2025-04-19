<?php

namespace Modules\Technician\Helpers;

use Modules\Technician\Exceptions\TechnicianException;

class TechnicianHelper
{
    /**
     * @throws TechnicianException
     */
    public static function getUserTechnician()
    {
        $technician = auth()->user()->technician;

        if(! $technician) {
            TechnicianException::notFound();
        }

        return $technician;
    }
}
