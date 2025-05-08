<?php

namespace Modules\Statistic\Services;

use Modules\Technician\Traits\TechnicianSetter;
use Modules\Ticket\Models\Ticket;

class TechnicianStatisticService
{
    use TechnicianSetter;

    public function handle() {
        $tickets = BaseStatisticService::getTickets(
            Ticket::query()->where('technician_id', $this->getTechnician()->id)
        );

        unset(
            $tickets['opened_tickets'],
        );

        return [ ...$tickets ];
    }
}
