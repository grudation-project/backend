<?php

namespace Modules\Ticket\Helpers;

class TicketTranslationHelper
{
    public static function en()
    {
        return [
            'ticket' => 'Ticket',
            'service_not_associated_with_manager' => 'Service must be associated with a manager',
            'ticket_assigned' => 'Ticket has been assigned to the technician',
            'ticket_resolved' => 'Ticket has been resolved',
        ];
    }

    public static function ar()
    {
        return [
            'ticket' => 'تذكرة',
            'service_not_associated_with_manager' => 'يجب أن تكون الخدمة مرتبطة بمدير',
            'ticket_assigned' => 'تم توجيه التذكرة للفني',
            'ticket_resolved' => 'تم إنهاء التذكرة',
        ];
    }
}
