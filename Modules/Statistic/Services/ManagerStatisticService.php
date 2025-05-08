<?php

namespace Modules\Statistic\Services;

use Modules\Manager\Traits\ManagerSetter;
use Modules\Technician\Models\Technician;
use Modules\Ticket\Models\Ticket;

class ManagerStatisticService
{
    use ManagerSetter;

    public function handle()
    {
        return [
            'technicians_count' => $this->getTechniciansCount(),
            ... BaseStatisticService::getTickets(
                Ticket::query()->where('manager_id', $this->getManager()->id)
            ),
        ];
    }

    private function getTechniciansCount() {
        return Technician::query()->where('manager_id', $this->getManager()->id)->count();
    }
}
