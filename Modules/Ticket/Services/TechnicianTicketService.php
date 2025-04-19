<?php

namespace Modules\Ticket\Services;

use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\Technician\Exceptions\TechnicianException;
use Modules\Technician\Traits\TechnicianSetter;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Builders\TicketBuilder;
use Modules\Ticket\Models\Ticket;

class TechnicianTicketService
{
    use TechnicianSetter;

    public function index(array $filters)
    {
        return Ticket::query()
            ->latest()
            ->where('technician_id', $this->getTechnician()->id)
            ->when(
                true, fn(TicketBuilder $b) => $b
                ->withDetails()
                ->applyFiltersForUsers($filters)
            )
            ->searchable(['title'])
            ->paginatedCollection();
    }

    /**
     * @throws TechnicianException
     */
    public function show($id)
    {
        return Ticket::query()
            ->where('technician_id', $this->getTechnician()->id)
            ->when(
                true, fn(TicketBuilder $b) => $b
                ->withDetails()
            )
            ->findOrFail($id);
    }

    /**
     * @throws TechnicianException
     */
    public function  resolve($id): void
    {
        $ticket = Ticket::query()
            ->where('technician_id', $this->getTechnician()->id)
            ->where('status', TicketStatusEnum::IN_PROGRESS)
            ->findOrFail($id);

        $ticket->forceFill([
            'status' => TicketStatusEnum::RESOLVED,
        ])
            ->save();

        UserTicketService::ticketNotification(
            $ticket->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_RESOLVED
        );
    }
}
