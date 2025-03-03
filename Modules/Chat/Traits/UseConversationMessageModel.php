<?php

namespace Modules\Chat\Traits;

use Modules\Chat\Models\ConversationMessage;

trait UseConversationMessageModel
{
    public function getConversationMessageObject(): ConversationMessage
    {
        return new (ConversationMessage::class);
    }

    public function getConversationMessageClass(): string
    {
        return ConversationMessage::class;
    }
}
