<?php

namespace Modules\FcmNotification\Enums;

enum NotificationTypeEnum
{
    const SYSTEM_NOTIFICATION = 'system_notification';
    const TICKET_CREATED = 'ticket_created';
    const TICKET_UPDATED = 'ticket_updated';
    const TICKET_ASSIGNED = 'ticket_assigned';
    const TICKET_RESOLVED = 'ticket_resolved';
    const CHAT = 'chat';
}
