<?php

namespace Modules\Ticket\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Pipeline;
use Modules\Ticket\Models\Filters\CreatedAtFilter;
use Modules\Ticket\Models\Filters\ServiceFilter;

class TicketBuilder extends Builder
{
    public function withDetails()
    {
        return $this->with([
            'user:id,name',
            'manager.user:id,name',
            'technician.user:id,name',
            'section.service:services.id,services.name',
        ]);
    }

    public function applyFiltersForUsers(array $filters)
    {
        return Pipeline::send($this)
            ->through([
                fn($query, $next) => CreatedAtFilter::handle($query, $next, $filters),
                fn($query, $next) => ServiceFilter::handle($query, $next, $filters),
            ])
            ->thenReturn();
    }
}
