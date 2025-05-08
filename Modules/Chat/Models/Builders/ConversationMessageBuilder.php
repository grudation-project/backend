<?php

namespace Modules\Chat\Models\Builders;

use App\Models\Builders\UserBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Chat\Helpers\ConversationMemberHelper;
use Modules\Chat\Models\ConversationMember;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Models\Scopes\MustHaveValidConversation;
use Modules\Chat\Models\Scopes\MyConversationScope;
use Modules\Markable\Traits\HasReactions;

class ConversationMessageBuilder extends Builder
{

    public function withPosition($conversationId, array $selectedColumns = ['*'], string $positionColumn = 'position'): ConversationMessageBuilder
    {
        return $this
            ->fromSub(ConversationMessage::query()->withoutGlobalScope(SoftDeletingScope::class)->where('conversation_id', $conversationId)->selectRaw("*,(ROW_NUMBER() over (order by created_at)) as $positionColumn"), $this->getModel()->getTable())
            ->select($selectedColumns);
    }

    public function whereValid($conversation, $userId = null): ConversationMessageBuilder
    {
        return $this
            ->where('conversation_id', $conversation)
            ->whereNotDeleted($conversation, $userId);
    }

    public function withMessageDetails($conversationId): ConversationMessageBuilder
    {
        return $this
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->withPosition($conversationId)
            ->with([
                'mediaSource',
                'member.user' => fn (BelongsTo|UserBuilder $b) => $b->select(['users.id', 'users.name', 'users.name'])->with('avatar'),
            ]);
    }

    public function withParentMessageDetails($conversationId): ConversationMessageBuilder
    {
        return $this->with([
            'parentMessage' => fn (self|BelongsTo $q) => $q->withMessageDetails($conversationId),
        ]);
    }

    public function whereValidPinned(): ConversationMessageBuilder
    {
        return $this->where(function (self $q) {
            $q->whereNotNull('pinned_till')->where('pinned_till', '>', now());
        });
    }

    public function whereNotDeleted($conversation, $userId = null): ConversationMessageBuilder
    {
        $member = ConversationMemberHelper::getCurrentMember($conversation, $userId);

        return $this->whereDoesntHave(
            'deletedMessages',
            fn (DeletedMessageBuilder|HasMany $b) => $b->where('conversation_member_id', '=', $member->id),
        );
    }

    public function withLatestMessageDetails($userId = null): ConversationMessageBuilder
    {
        $userId = $userId ?: auth()->id();

        return $this
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                MustHaveValidConversation::class,
            ])
            ->when(true, fn (self $b) => $b->whereNotDeletedConversation($userId))
            ->whereDoesntHave('deletedMessages.conversationMember', fn ($q) => $q->where('member_id', $userId))
            ->select(['id', 'content', 'conversation_id', 'type', 'record_duration', 'seen', 'delivered', 'conversation_member_id', 'created_at', 'deleted_at'])
            ->latest('conversation_messages.created_at');
    }

    public function withMemberId(): ConversationMessageBuilder
    {
        return $this->addSelect([
            'member_id' => ConversationMember::query()
                ->whereColumn('conversation_members.id', 'conversation_messages.conversation_member_id')
                ->select('member_id')
                ->limit(1),
        ]);
    }

    public function whereNotDeletedConversation($userId = null): ConversationMessageBuilder
    {
        return $this->where(function (self $builder) use ($userId) {
            $userId = $userId ?: auth()->id();

            return $builder->whereNull('deleted_by_user_id')
                ->orWhere('deleted_by_user_id', '!=', $userId);
        });
    }
}
