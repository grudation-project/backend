<?php

namespace Modules\Ticket\Services;

use App\Exceptions\ValidationErrorsException;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\Manager\Traits\ManagerSetter;
use Modules\Technician\Services\TechnicianService;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Builders\TicketBuilder;
use Modules\Ticket\Models\Ticket;

class ManagerTicketService
{
    use ManagerSetter;

    public function index(array $filters)
    {
        return Ticket::query()
            ->latest()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails()->applyFiltersForUsers($filters))
            ->where('manager_id', $this->getManager()->id)
            ->searchable(['title'])
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Ticket::query()
            ->latest()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails())
            ->where('manager_id', $this->getManager()->id)
            ->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $ticket = Ticket::query()
            ->where('manager_id', $this->getManager()->id)
            ->whereIn('status', [TicketStatusEnum::PENDING, TicketStatusEnum::IN_PROGRESS])
            ->findOrFail($id);

        $ticket->update($data);

        return $this->show($id);
    }

    /**
     * @throws ValidationErrorsException
     */
    public function assign(array $data, $id)
    {
        $ticket = Ticket::query()
            ->where('manager_id', $this->getManager()->id)
            ->whereIn('status', [
                TicketStatusEnum::PENDING,
                TicketStatusEnum::IN_PROGRESS,
            ])
            ->findOrFail($id);

        TechnicianService::exists($this->getManager()->id, $data['technician_id'], $ticket->section_id);

        $ticket->update([
            ...$data,
            'status' => TicketStatusEnum::IN_PROGRESS,
            'assigned_at' => $data['technician_id'] != $ticket->technician_id ? now() : $ticket->assigned_at,
        ]);

        UserTicketService::ticketNotification(
            $ticket->technician->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_ASSIGNED
        );
        UserTicketService::ticketNotification(
            $ticket->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_ASSIGNED
        );
    }

    public function  resolve($id)
    {
        $ticket = Ticket::query()
            ->where('manager_id', $this->getManager()->id)
            ->whereIn('status', [TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::PENDING])
            ->findOrFail($id);

        $ticket->forceFill([
            'status' => TicketStatusEnum::RESOLVED,
        ])->save();

        UserTicketService::ticketNotification(
            $ticket->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_RESOLVED
        );
    }
}
