<?php

namespace Modules\Statistic\Services;

use Modules\Manager\Models\Manager;
use Modules\Service\Models\Service;

class StatisticService
{
    public function handle() {
        return [
            'services_count' => $this->servicesCount(),
            'managers_count' => $this->getManagersCount(),
            ... BaseStatisticService::getTickets(),
        ];
    }

    private function servicesCount() {
        return Service::query()->count();
    }

    private function getManagersCount() {
        return Manager::query()->count();
    }
}
