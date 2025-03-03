<?php

namespace Modules\Chat\Traits;

use Modules\Chat\Models\Conversation;

trait UseConversationModel
{
    public function getConversationModel(): Conversation
    {
        return new (Conversation::class);
    }
}
