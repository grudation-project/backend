<?php

namespace Modules\Statistic\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Builders\TicketBuilder;
use Modules\Ticket\Models\Ticket;
use Modules\Ticket\Transformers\TicketResource;

class BaseStatisticService
{
    public static function getTickets(?Builder $builder = null) {
        $allTickets = $openedTickets = $inProcessingTickets = $closedTickets = 0;
        $tickets = ($builder ?: Ticket::query())
            ->latest()
            ->select(['id', 'status', 'created_at'])
            ->cursor();

        $recentTickets = $tickets = ($builder ?: Ticket::query())
            ->latest()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails())
            ->limit(5)
            ->get();

        $mappedGroupedTicketsCount = [];

        $tickets->each(function ($ticket) use (&$allTickets, &$openedTickets, &$closedTickets, &$inProcessingTickets, &$mappedGroupedTicketsCount) {
            $allTickets++;

            switch($ticket->status) {
                case TicketStatusEnum::PENDING:
                    $openedTickets++;
                    break;
                case TicketStatusEnum::IN_PROGRESS:
                    $inProcessingTickets++;
                    break;
                case TicketStatusEnum::RESOLVED:
                    $closedTickets++;
                    break;
            }

            $year = $ticket->created_at->format('Y');
            $mappedGroupedTicketsCount[$year] = $mappedGroupedTicketsCount[$year] ?? 0;
            $mappedGroupedTicketsCount[$year]++;
        });

        $yearGroupedTickets = [];

        for($i = now()->subYears(5)->format('Y'); $i <= now()->format('Y'); $i++) {
            if(!isset($mappedGroupedTicketsCount[$i])) {
                $yearGroupedTickets[] = [
                    'year' => $i + 0,
                    'count' => 0,
                ];
            } else {
                $yearGroupedTickets[] = [
                    'year' => $i + 0,
                    'count' => $mappedGroupedTicketsCount[$i],
                ];
            }
        }

        return [
            'all_tickets' => $allTickets,
            'opened_tickets' => $openedTickets,
            'in_processing_tickets' => $inProcessingTickets,
            'closed_tickets' => $closedTickets,
            'annual_tickets_average' => $yearGroupedTickets,
            'recent_tickets' => TicketResource::collection($recentTickets),
        ];
    }
}
