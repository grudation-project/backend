<?php

namespace Modules\Ticket\Models\Filters;

class CreatedAtFilter
{
    public static function handle($query, $next, array $filters)
    {
        if(isset($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }

        if(isset($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }

        return $next($query);
    }
}
