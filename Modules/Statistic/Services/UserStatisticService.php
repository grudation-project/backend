<?php

namespace Modules\Statistic\Services;

use Modules\Ticket\Models\Ticket;

class UserStatisticService
{
    public function handle()
    {
        return [
            ... BaseStatisticService::getTickets(Ticket::query()->where('user_id', auth()->id())),
        ];
    }
}
