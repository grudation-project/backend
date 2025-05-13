<?php

namespace Modules\Chat\Services;

use App\Exceptions\ValidationErrorsException;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Chat\Enums\ConversationTypeEnum;
use Modules\Chat\Events\ConversationUpdatedEvent;
use Modules\Chat\Exceptions\ConversationException;
use Modules\Chat\Helpers\ChatUserHelper;
use Modules\Chat\Helpers\ConversationMemberHelper;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Models\Scopes\MyConversationScope;
use Modules\Chat\Traits\UseChatUserModel;

class ConversationService
{
    use UseChatUserModel;

    public function index()
    {
        return Conversation::query()
            ->select('*')
            ->when(true, fn (ConversationBuilder $b) => $b->withConversationDetails()->handleSearch())
            // ->orderByRaw('case when pinned = 1 then 0 else 1 end,latest_message_time DESC')
            ->paginatedCollection();
    }

    public function show($id, $loggedUserId = null, $operator = '<>')
    {
        return Conversation::query()
            ->when(true, fn (ConversationBuilder $b) => $b->withConversationDetails($loggedUserId, $operator))
            ->findOrFail($id);
    }

    /**
     * @throws ConversationException
     * @throws ValidationErrorsException
     */
    public function store(array $data)
    {
        if (isset($data['user_id'])) {
            $member = $this->getChatUserModel()
                ->where('id', $data['user_id'])
                ->whereIn('type', ChatUserHelper::allowedTypes())
                ->where('id', '<>', auth()->id())
                ->first();

            if (! $member) {
                throw new ValidationErrorsException([
                    'user_id' => 'User not found',
                ]);
            }

            $this->assertAllowedToChatWith($member, ConversationTypeEnum::PRIVATE);
        } else {
            $this->assertAllowedToChatWith(auth()->user(), ConversationTypeEnum::MINE);
        }

        $conversation = null;
        $myConversation = Conversation::query()
            ->where('type', ConversationTypeEnum::MINE)
            ->first();

        if ($data['type'] == ConversationTypeEnum::MINE && $myConversation) {
            ConversationException::alreadyExists();
        }

        if ($data['type'] == ConversationTypeEnum::PRIVATE) {
            $conversation = Conversation::query()
                ->withoutGlobalScope(MyConversationScope::class)
                ->when(
                    true,
                    fn (ConversationBuilder $b) => $b
                        ->whereHas('members', fn ($b) => $b->where('member_id', auth()->id()))
                        ->whereHas('members', fn ($b) => $b->where('member_id', $data['user_id']))
                )
                ->with('members')
                ->where('type', ConversationTypeEnum::PRIVATE)
                ->first();

            if ($conversation && $conversation->members[0]->member_id == $conversation->members[1]->member_id) {
                throw new ValidationErrorsException([
                    'user_id' => translate_error_message('user', 'not_exists'),
                ]);
            }

        }

        if ($conversation) {
            return $conversation;
            ConversationException::alreadyExists();
        }

        return DB::transaction(function () use ($data, $conversation) {
            $conversation = Conversation::create([
                'type' => $data['type'],
            ]);

            $conversation->members()->createMany([
                [
                    'conversation_id' => $conversation->id,
                    'member_id' => auth()->id(),
                ],
                [
                    'conversation_id' => $conversation->id,
                    'member_id' => $data['type'] == ConversationTypeEnum::PRIVATE ? $data['user_id'] : auth()->id(),
                ],
            ]);

            return $conversation;
        });
    }

    /**
     * @throws ConversationException
     */
    public function pin(array $data, $conversation): void
    {
        $conversation = $this->getBasicConversation($conversation);
        $shouldPin = $data['pin'];

        if ($shouldPin == $conversation->pinned) {
            return;
        }

        $pinnedConversationsCount = Conversation::query()
            ->where('pinned', true)
            ->count();

        $maxPinnedConversations = 3;
        if ($shouldPin && $pinnedConversationsCount == $maxPinnedConversations) {
            ConversationException::maximumPinnedConversations([
                'count' => $maxPinnedConversations,
            ]);
        }

        $conversation->forceFill(['pinned' => $shouldPin])->save();
        event(new ConversationUpdatedEvent(auth()->id(), $this->show($conversation->id)));
    }

    public function getByUser($userId)
    {
        $id = Conversation::query()
            ->whereHas(
                'members',
                fn ($b) => $b->where('member_id', $userId)->where('member_id', '<>', auth()->id())
            )
            ->value('id');

        return $id ? $this->show($id) : null;
    }

    public function getOtherUser($userId)
    {
        return User::query()
            ->select(ConversationBuilder::$otherUserSelectedColumns)
            ->whereIn('type', ChatUserHelper::allowedTypes())
            ->with('avatar')
            ->findOrFail($userId);
    }

    public function markAsSeen($conversationId): void
    {
        $conversation = $this->getBasicConversation($conversationId);

        $member = ConversationMemberHelper::getCurrentMember($conversation);

        ConversationMessage::query()
            ->where('conversation_id', $conversationId)
            ->where('conversation_member_id', '<>', $member->id)
            ->where('seen', false)
            ->update(['seen' => true]);
    }

    public function destroy($conversationId): void
    {
        $conversation = $this->getBasicConversation($conversationId);

        DB::transaction(function () use ($conversation) {
            $conversation
                ->messages()
                ->whereNotNull('deleted_by_user_id')
                ->where('deleted_by_user_id', '<>', auth()->id())
                ->forceDelete();

            $conversation
                ->messages()
                ->whereNull('deleted_by_user_id')
                ->update(['deleted_by_user_id' => auth()->id()]);
        });
    }

    private function getBasicConversation($conversationId)
    {
        return Conversation::query()
            ->when(true, fn (ConversationBuilder $builder) => $builder->whereHasMessages())
            ->findOrFail($conversationId);
    }

    private function allowedToChat(User $target, $conversationType, ?User $from = null): bool
    {
        return true;
        $from = $from ?: auth()->user();
        $fromType = UserTypeEnum::getUserType($from);
        $targetType = UserTypeEnum::getUserType($target);

        if ($fromType === UserTypeEnum::ADMIN && $targetType !== UserTypeEnum::ADMIN_EMPLOYEE) {
            return true;
        }

        if ($conversationType == ConversationTypeEnum::MINE && $from->id != $target->id || ! ChatUserHelper::allowedToChat($fromType)) {
            throw new ValidationErrorsException([
                'user_id' => translate_error_message('user', 'not_exists'),
            ]);
        }

        $allowed = match ($fromType) {
            UserTypeEnum::DELIVERY_MAN, UserTypeEnum::MAINTENANCE => in_array($targetType, [
                UserTypeEnum::ADMIN,
                UserTypeEnum::NORMAL_USER,
                UserTypeEnum::VENDOR,
            ]),
            UserTypeEnum::NORMAL_USER => in_array($targetType, [
                UserTypeEnum::MAINTENANCE,
                UserTypeEnum::DELIVERY_MAN,
                UserTypeEnum::ADMIN,
                UserTypeEnum::VENDOR,
            ]),
            UserTypeEnum::VENDOR => in_array($targetType, [
                UserTypeEnum::DELIVERY_MAN,
                UserTypeEnum::ADMIN,
            ]),
            default => false,
        };

        if (! $allowed) {
            return false;
        }

        return true;
    }

    /**
     * @throws ConversationException
     * @throws ValidationErrorsException
     */
    private function assertAllowedToChatWith(User $target, $conversationType, ?User $from = null): void
    {
        if (! $this->allowedToChat($target, $conversationType, $from)) {
            ConversationException::notAllowedToChatWith();
        }
    }
}
