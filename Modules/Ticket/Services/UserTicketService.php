<?php

namespace Modules\Ticket\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\Notification;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\FcmNotification\Notifications\FcmNotification;
use Modules\Manager\Models\Manager;
use Modules\Service\Models\Section;
use Modules\Service\Services\AdminSectionService;
use Modules\Service\Services\AdminServiceLogic;
use Modules\Technician\Models\Technician;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Builders\TicketBuilder;
use Modules\Ticket\Models\Ticket;

class UserTicketService
{
    public function index(array $filters)
    {
        return Ticket::query()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails()->applyFiltersForUsers($filters))
            ->where('user_id', auth()->id())
            ->latest()
            ->searchable(['title'])
            ->paginatedCollection();
    }

    public function show($id)
    {
        return Ticket::query()
            ->when(true, fn(TicketBuilder $b) => $b->withDetails())
            ->where('user_id', auth()->id())
            ->findOrFail($id);
    }

    public function store(array $data)
    {
        $section = AdminSectionService::exists($data['section_id']);
        $service = $section->service;
        $manager = $service->manager;

        if (! $manager) {
            throw new ValidationErrorsException([
                'service_id' => translate_word('service_not_associated_with_manager'),
            ]);
        }

        $technician = $this->getAutomaitcAssignedTechnician($manager, $section);
        $technicianId = $technician ? $technician->id : null;

        $ticket = Ticket::query()->create($data + [
            'user_id' => auth()->id(),
            'manager_id' => $manager->id,
            'status' => $technicianId ? TicketStatusEnum::IN_PROGRESS : TicketStatusEnum::PENDING,
            'assigned_at' => $technicianId ? now() : null,
            'technician_id' => $technicianId,
        ]);

        self::ticketNotification($ticket->manager->user, $ticket->id, NotificationTypeEnum::TICKET_CREATED);

        if ($technicianId) {
            self::ticketNotification($ticket->technician->user, $ticket->id, NotificationTypeEnum::TICKET_ASSIGNED);
        }

        return $this->show($ticket->id);
    }

    public function update(array $data, $id)
    {
        $ticket = Ticket::query()
            ->where('user_id', auth()->id())
            ->where('status', TicketStatusEnum::PENDING)
            ->findOrFail($id);

        $sectionId = $ticket->section_id;
        $managerId = $ticket->manager_id;
        $technicianId = null;

        if (isset($data['section_id'])) {
            $section = AdminSectionService::exists($data['section_id']);
            $service = $section->service;
            $manager = $service->manager;

            if (! $manager) {
                throw new ValidationErrorsException([
                    'service_id' => translate_word('service_not_associated_with_manager'),
                ]);
            }

            $sectionId = $section->id;
            $managerId = $manager->id;
            $technician = $this->getAutomaitcAssignedTechnician($manager, $section);
            $technicianId = $technician ? $technician->id : null;
        }

        $technicianPayload = [];

        if ($technicianId) {
            $technicianPayload = [
                'technician_id' => $technicianId,
                'assigned_at' => now(),
            ];
        }

        $ticket->update($data + [
            'manager_id' => $managerId,
            'section_id'  => $sectionId,
            ...$technicianPayload,
        ]);

        self::ticketNotification(
            $ticket->manager->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_UPDATED
        );

        if ($technicianId) {
            self::ticketNotification(
                $ticket->technician->user,
                $ticket->id,
                NotificationTypeEnum::TICKET_ASSIGNED
            );
        }

        return $this->show($ticket->id);
    }

    private function getAutomaitcAssignedTechnician(Manager $manager, Section $section)
    {
        if ($manager->automatic_assignment) {
            return Technician::query()
                ->where('section_id', $section->id)
                ->where('manager_id', $manager->id)
                ->withCount('liveTickets')
                ->orderByRaw('live_tickets_count ASC')
                ->first();
        }

        return null;
    }

    public static function ticketNotification($user, $ticketId, $notificationType)
    {
        switch ($notificationType) {
            case NotificationTypeEnum::TICKET_CREATED:
                $title = 'ticket_created_title';
                $body = 'ticket_created_body';
                break;

            case NotificationTypeEnum::TICKET_UPDATED:
                $title = 'ticket_updated_title';
                $body = 'ticket_updated_body';
                break;

            case NotificationTypeEnum::TICKET_ASSIGNED:
                $title = 'ticket_assigned_title';
                $body = 'ticket_assigned_body';
                break;

            default:
                $title = 'ticket_resolved_title';
                $body = 'ticket_resolved_body';
                break;
        }

        Notification::send($user, new FcmNotification(
            $title,
            $body,
            additionalData: [
                'type' => $notificationType,
                'model_id' => $ticketId,
            ],
            shouldTranslate: [
                'title' => true,
                'body' => true,
            ]
        ));
    }
}
