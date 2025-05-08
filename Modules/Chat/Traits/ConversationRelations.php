<?php

namespace Modules\Chat\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Chat\Models\ConversationMember;
use Modules\Chat\Models\ConversationMessage;

trait ConversationRelations
{
    use UseChatUserModel;

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ConversationMessage::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            $this->getUserClass(),
            ConversationMember::class,
            'conversation_id',
            'id',
            'id',
            'member_id'
        );
    }

    public function members(): HasMany
    {
        return $this->hasMany(ConversationMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class);
    }

    public function unseenMessages(): HasMany
    {
        return $this->messages()->where('seen', false);
    }

    public function otherUser()
    {
        return $this->hasOneThrough(
            $this->getUserClass(),
            ConversationMember::class,
            'conversation_id',
            'id',
            'id',
            'member_id',
        );
    }

    public function member(): HasOne
    {
        return $this->members()->one();
    }

    public function latestReaction()
    {
        return $this->hasOneThrough(
            ReactionModel::class,
            ConversationMessage::class,
            'conversation_id',
            'markable_id',
            'id',
            'id'
        )
            ->latest('markable_reactions.created_at')
            ->where('markable_type', ConversationMessage::class);
    }
}
