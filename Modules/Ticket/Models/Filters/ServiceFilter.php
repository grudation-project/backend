<?php

namespace Modules\Ticket\Models\Filters;

class ServiceFilter
{
    public static function handle($query, $next, array $filters)
    {
        if(isset($filters['service_id'])) {
            $query->whereHas('section', fn($q) => $q->where('service_id', $filters['service_id']));
        }

        return $next($query);
    }
}
