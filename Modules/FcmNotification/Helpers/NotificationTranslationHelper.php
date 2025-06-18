<?php

namespace Modules\FcmNotification\Helpers;

class NotificationTranslationHelper
{
    public static function en(): array
    {
        return [
            'you_have_new_message' => 'New Message',
            'notification' => 'Notification',
            'notifications' => 'Notifications',
            'ticket_created_title' => 'New Ticket Created',
            'ticket_created_body' => 'A new ticket has been created by the user, click to view.',
            'ticket_updated_title' => 'Ticket Updated',
            'ticket_updated_body' => 'Ticket information has been updated by the user, click to view updated',
            'ticket_assigned_title' => 'Ticket Assigned',
            'ticket_assigned_body' => 'Ticket has been assigned to the technician, click to view.',
            'ticket_resolved_title' => 'Ticket Resolved',
            'ticket_resolved_body' => 'Ticket has been resolved by the technician, click to view.',
            'message_title' => 'New message on ticket #:ticket_id',
        ];
    }

    public static function ar(): array
    {
        return [
            'you_have_new_message' => 'رسالة جديدة',
            'notification' => 'الإشعار',
            'notifications' => 'الأشعارات',
            'ticket_created_title' => 'تذكرة جديدة تم إنشاؤها',
            'ticket_created_body' => 'تم إنشاء تذكرة جديدة بواسطة المستخدم، انقر لعرضها.',
            'ticket_updated_title' => 'تذكرة محدثة',
            'ticket_updated_body' => 'تم تحديث معلومات التذكرة بواسطة المستخدم، انقر لعرض التحديثات',
            'ticket_assigned_title' => 'تذكرة مخصصة',
            'ticket_assigned_body' => 'تم تعيين التذكرة للفني، انقر لعرضها.',
            'ticket_resolved_title' => 'تذكرة محلية',
            'ticket_resolved_body' => 'تم حل التذكرة بواسطة الفني، انقر لعرضها.',
            'message_title' => 'رسالة جديدة في التذكرة #:ticket_id',
        ];
    }

    public static function fr(): array
    {
        return [
            'you_have_new_message' => 'Nouveau message',
            'notification' => 'Notification',
            'notifications' => 'Notifications',
        ];
    }
}
