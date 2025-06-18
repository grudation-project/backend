<?php

namespace Modules\Chat\Helpers;

class ChatTranslationHelper
{
    public static function en()
    {
        return [
            'conversation' => 'Conversation',
            'message' => 'Message',
            'maximum_pinned_conversations' => 'You Can\'t pin more than :count conversations',
            'conversation_pinned' => 'Conversation Pinned',
            'conversation_unpinned' => 'Conversation Unpinned',
            'message_pinned' => 'Message Pinned',
            'message_unpinned' => 'Message Unpinned',
            'not_member_conversation' => 'You are not a member of this conversation',
            'conversation_seen' => 'You read all messages in this conversation',
            'not_allowed_to_chat_with_user' => 'You are not allowed to chat with this user',
            'message_title' => 'New message on ticket #:ticketId',
        ];
    }

    public static function ar()
    {
        return [
            'conversation' => 'المحادثه',
            'message' => 'الرساله',
            'maximum_pinned_conversations' => 'لا يمكنك تثبيت أكثر من :count محادثه',
            'conversation_pinned' => 'لقد قمت بتثبيت المحادثه',
            'conversation_unpinned' => 'لقد قمت بإلغاء تثبيت المحادثه',
            'message_pinned' => 'لقد قمت بتثبيت الرساله',
            'message_unpinned' => 'لقد قمت بإلغاء تثبيت الرساله',
            'not_member_conversation' => 'أنت لست عضو في هذه المحادثه',
            'conversation_seen' => 'لقد قمت بقراءه جميع الرسائل في هذه المحادثه',
            'not_allowed_to_chat_with_user' => 'غير مسموح لك بالدردشه مع هذا المستخدم',
        ];
    }

    public static function ku()
    {
        return [
            'conversation' => 'گفتوگۆ',
            'message' => 'پەیام',
            'maximum_pinned_conversations' => 'ناتوانیت زیاتر لە :count گفتوگۆ تێپیڕی بکەیت',
            'conversation_pinned' => 'گفتوگۆ تێپیڕی کرا',
            'conversation_unpinned' => 'گفتوگۆ تێپیڕی نەکرا',
            'message_pinned' => 'پەیام تێپیڕی کرا',
            'message_unpinned' => 'پەیام تێپیڕی نەکرا',
            'not_member_conversation' => 'تۆ ئەندامی ئەم گفتوگۆیە',
            'conversation_seen' => 'تۆ هەموو پەیامەکان لەم گفتوگۆیە خوێندووە',
            'not_allowed_to_chat_with_user' => 'تۆ ناتوانیت بەم بەکارهێنەرە دروست بکەیت',
        ];
    }
}
