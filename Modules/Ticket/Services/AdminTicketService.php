<?php

namespace Modules\Ticket\Services;

use Modules\Ticket\Models\Builders\TicketBuilder;
use Modules\Ticket\Models\Ticket;

class AdminTicketService
{
    public function index(array $filters)
    {
        return Ticket::query()
            ->latest()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails()->applyFiltersForUsers($filters))
            ->searchable(['title'])
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Ticket::query()
            ->latest()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails())
            ->findOrFail($id);
    }
}
