<?php

namespace Modules\Chat\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Chat\Enums\ConversationTypeEnum;
use Modules\Chat\Models\ConversationMember;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Models\Scopes\MustHaveValidConversation;
use Modules\Markable\Entities\Builders\ReactionModelBuilder;

class ConversationBuilder extends Builder
{
    public static array $otherUserSelectedColumns = [
        'users.id',
        'users.name',
        'users.name',
        'users.type',
        'users.chat_active',
        'users.last_time_seen',
    ];

    public function withConversationDetails($loggedUserId = null, string $operator = '<>'): ConversationBuilder
    {
        $loggedUserId = $loggedUserId ?: auth()->id();

        return $this
            ->whereMember($loggedUserId)
            ->withOtherUserDetails($loggedUserId, $operator)
            ->withLatestMessageDetails($loggedUserId)
            ->withUnseenMessagesCount($loggedUserId)
            ->selectLatestMessageTime(userId: $loggedUserId);
    }

    public function whereHasMessages($userId = null): ConversationBuilder
    {
        return $this->whereHas('messages', fn (ConversationMessageBuilder|HasMany $b) => $b->whereNotDeletedConversation($userId));
    }

    public function withUnseenMessagesCount($userId = null): ConversationBuilder
    {
        $userId = $userId ?: auth()->id();
        $seen = request()->input('seen');

        return $this->withCount([
            'unseenMessages' => fn (ConversationMessageBuilder $b) => $b->withoutGlobalScope(MustHaveValidConversation::class)->whereHas(
                'member', fn ($q) => $q->where('member_id', '<>', $userId)
            ),
        ])
            ->when(! is_null($seen), fn ($q) => $q
                ->when($seen, fn (Builder $q2) => $q2->having('unseen_messages_count', '=', 0))  // Correct comparison for seen
                ->when(! $seen, fn (Builder $q2) => $q2->having('unseen_messages_count', '>', 0))  // Correct comparison for unseen
            );
    }

    public function selectLatestMessageTime(string $column = 'latest_message_time', $userId = null): ConversationBuilder
    {
        return $this->addSelect([
            $column => ConversationMessage::query()
                ->when(true, fn (ConversationMessageBuilder $b) => $b->whereNotDeletedConversation($userId))
                ->select('created_at')
                ->whereColumn('conversations.id', 'conversation_messages.conversation_id')
                ->latest('conversation_messages.created_at')
                ->limit(1),
        ]);
    }

    public function withLatestMessageDetails($userId = null): ConversationBuilder
    {
        return $this
            ->with([
                'latestMessage' => fn (ConversationMessageBuilder|HasOne $b) => $b->withLatestMessageDetails($userId)->withMemberId(),
            ]);
    }

    public function withOtherUserDetails($loggedUserId = null, string $operator = '<>')
    {
        $loggedUserId = $loggedUserId ?: auth()->id();

        return $this
            ->with([
                'otherUser' => fn ($q) => $q->select(self::$otherUserSelectedColumns)
                    ->with('avatar')
                    ->where('users.id', $operator, $loggedUserId),
            ]);
    }

    public function handleSearch()
    {
        $columns = ['name', 'name', 'email', 'phone'];

        return $this->when(! is_null(request()->input('handle')), function (ConversationBuilder $query) use ($columns) {
            $query->where(function (ConversationBuilder $query) use ($columns) {
                $query->where('type', ConversationTypeEnum::MINE)->whereHas('users', fn ($q) => $q->searchable($columns));
            })->orWhereHas('users', function ($query) use ($columns) {
                $query->where('users.id', '<>', auth()->id())->searchable($columns);
            });
        });
    }

    public function whereMember($userId = null, bool $withoutDeleted = false): ConversationBuilder
    {
        $userId = $userId ?: auth()->id();

        return $this->whereHas('members', fn ($q) => $q->where('member_id', $userId)->when($withoutDeleted, fn($q) => $q->withoutGlobalScopes()));
    }

    public function withCurrentMember($userId = null): ConversationBuilder
    {
        $userId = $userId ?: auth()->id();

        return $this->with([
            'member' => fn (ConversationMember|HasOne $builder) => $builder->where('member_id', $userId),
        ]);
    }
}
