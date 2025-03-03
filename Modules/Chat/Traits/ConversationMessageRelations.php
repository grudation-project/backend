<?php

namespace Modules\Chat\Traits;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Models\ConversationMember;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Models\DeletedMessage;

trait ConversationMessageRelations
{
    use UseChatUserModel;

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function mediaSource()
    {
        return MediaHelper::mediaRelationship($this, 'chat_media', ['name', 'size']);
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'parent_id');
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            $this->getUserClass(),
            ConversationMember::class,
            'conversation_id',
            'id',
            'conversation_id',
            'member_id'
        );
    }

    public function otherUser(): HasOneThrough
    {
        return $this->user();
    }

    public function deletedMessages(): HasMany
    {
        return $this->hasMany(DeletedMessage::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(ConversationMember::class, 'conversation_member_id');
    }
}
