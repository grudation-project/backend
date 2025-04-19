<?php

namespace Modules\Ticket\Services;

use App\Exceptions\ValidationErrorsException;
use Illuminate\Support\Facades\Notification;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\FcmNotification\Notifications\FcmNotification;
use Modules\Service\Services\AdminServiceLogic;
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
        $service = AdminServiceLogic::exists($data['service_id']);
        $manager = $service->manager;

        if(! $manager) {
            throw new ValidationErrorsException([
                'service_id' => translate_word('service_not_associated_with_manager'),
            ]);
        }

        $ticket = Ticket::query()->create($data + [
           'user_id' => auth()->id(),
           'manager_id' => $manager->id,
           'status' => TicketStatusEnum::PENDING,
        ]);

        self::ticketNotification($ticket->manager->user, $ticket->id, NotificationTypeEnum::TICKET_CREATED);

        return $this->show($ticket->id);
    }

    public function update(array $data, $id)
    {
        $ticket = Ticket::query()
            ->where('user_id', auth()->id())
            ->where('status', TicketStatusEnum::PENDING)
            ->findOrFail($id);

        $serviceId = $ticket->service_id;
        $managerId = $ticket->manager_id;

        if(isset($data['service_id']))
        {
            $service = AdminServiceLogic::exists($data['service_id']);
            $manager = $service->manager;

            if(! $manager) {
                throw new ValidationErrorsException([
                    'service_id' => translate_word('service_not_associated_with_manager'),
                ]);
            }

            $serviceId = $service->id;
            $managerId = $manager->id;
        }

        $ticket->update($data + [
            'manager_id' => $managerId,
            'service_id'  => $serviceId,
        ]);

        self::ticketNotification(
            $ticket->manager->user,
            $ticket->id,
            NotificationTypeEnum::TICKET_UPDATED
        );

        return $this->show($ticket->id);
    }

    public static function ticketNotification($user, $ticketId, $notificationType)
    {
        switch($notificationType) {
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
