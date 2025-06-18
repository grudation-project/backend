<?php

namespace Modules\Chat\Services;

use App\Exceptions\ValidationErrorsException;
use App\Helpers\PaginationHelper;
use App\Services\FileOperationService;
use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Chat\Enums\ConversationTypeEnum;
use Modules\Chat\Enums\MessageTypeEnum;
use Modules\Chat\Events\ConversationUpdatedEvent;
use Modules\Chat\Events\MessageDeletedEvent;
use Modules\Chat\Events\NewMessageEvent;
use Modules\Chat\Helpers\ConversationHelper;
use Modules\Chat\Helpers\ConversationMemberHelper;
use Modules\Chat\Jobs\BulkConversationsBroadcast;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Builders\ConversationMessageBuilder;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Models\ConversationMember;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Models\Scopes\MustHaveValidConversation;
use Modules\Chat\Transformers\ConversationMessageResource;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\FcmNotification\Notifications\FcmNotification;
use Modules\Manager\Helpers\ManagerHelper;
use Modules\Map\Helpers\PointHelper;
use Modules\Technician\Helpers\TechnicianHelper;
use Modules\Ticket\Models\Ticket;
use NotificationChannels\Fcm\FcmChannel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ConversationMessageService
{
    protected FileOperationService $fileOperationService;

    public function __construct(FileOperationService $fileOperationService, private ConversationService $conversationService)
    {
        $this->fileOperationService = $fileOperationService;
    }

    public function index($conversationId)
    {
        $ticketId = request()->input('ticket_id');

        $conversations = ConversationMessage::query()
            ->when(
                true,
                fn(ConversationMessageBuilder $b) => $b
                    ->whereValid($conversationId)
                    ->whereNotDeletedConversation()
                    ->withMessageDetails($conversationId)
                    ->withParentMessageDetails($conversationId)
            )
            ->when(!is_null($ticketId), fn($q) => $q->where('ticket_id', $ticketId))
            ->searchable(['content'])
            //            ->latest('conversation_messages.created_at')
            ->paginatedCollection();

        return self::prepareMessages($conversations);
    }

    public function show($conversationId, $messageId)
    {
        $message = ConversationMessage::query()
            ->when(
                true,
                fn(ConversationMessageBuilder $b) => $b
                    ->whereValid($conversationId)
                    ->withMessageDetails($conversationId)
                    ->withParentMessageDetails($conversationId)
            )
            ->findOrFail($messageId);

        return self::prepareMessage($message);
    }

    public function store(array $data, $conversationId): array
    {
        $userType = UserTypeEnum::getUserType();

        if (isset($data['ticket_id'])) {
            $ticket = Ticket::query()
                ->when($userType == UserTypeEnum::USER, fn($q) => $q->where('user_id', auth()->id()))
                ->when($userType == UserTypeEnum::MANAGER, fn($q) => $q->where('manager_id', ManagerHelper::getUserManager()->id))
                ->when($userType == UserTypeEnum::TECHNICIAN, fn($q) => $q->where('technician_id', TechnicianHelper::getUserTechnician()->id))
                ->find($data['ticket_id']);

            if (! $ticket) {
                throw new ValidationErrorsException([
                    'ticket_id' => translate_error_message('ticket', 'not_exists'),
                ]);
            }
        }

        [$message, $conversation, $member] = DB::transaction(function () use ($data, $conversationId) {
            $conversation = $this->findConversation($conversationId);
            ConversationHelper::setConversationContainerInstance($conversation);

            $this->validateParentMessage($data, $conversation);

            // if (isset($data['location'])) {
            //     $data['location'] = PointHelper::destructPoint($data['location']);
            // }

            $member = $this->findConversationMember($conversation);

            $message = $this->createMessage($data, $conversation, $member);
            $otherMember = $conversation->users()->where('users.id', '<>', auth()->id())->firstOrFail();

            $this->handleMedia($message, $data);

            return [$this->show($conversation->id, $message->id), $conversation, $otherMember];
        });

        $this->broadcastCreatedMessage($conversation->id, $message);

        self::notifyUser($conversation, $message);

        return [$message, $member->user];
    }

    /**
     * @throws ValidationErrorsException
     */
    public function forward(array $data, $conversationId, $messageId): ConversationMessage
    {
        $message = $this->getBasicMessage($conversationId, $messageId);

        try {
            $targetConversation = $this->findConversation($data['target_conversation_id']);
        } catch (Exception $e) {
            throw new ValidationErrorsException([
                'target_conversation_id' => $e->getMessage(),
            ]);
        }

        $targetMember = $this->findConversationMember($targetConversation);

        $newMessage = DB::transaction(function () use ($message, $targetMember, $targetConversation) {
            $newMessage = $this->replicateMessage($message, $targetMember, $targetConversation->id);

            return $this->show($newMessage->conversation_id, $newMessage->id);
        });

        $this->broadcastCreatedMessage($targetConversation->id, $newMessage);

        self::notifyUser($targetConversation, $newMessage);

        return $newMessage;
    }

    public function destroy($conversationId, $messageId)
    {
        $deleteForAll = (bool) request()->input('delete_for_all');
        $conversationMember = ConversationMemberHelper::getCurrentMember($conversationId);
        $conversation = ConversationHelper::getConversation($conversationId);

        if ($conversation->type != ConversationTypeEnum::MINE) {
            $message = $this->getBasicMessage($conversationId, $messageId);
        } else {
            $message = ConversationMessage::query()
                ->withoutGlobalScope(MustHaveValidConversation::class)
                ->when(true, fn(ConversationMessageBuilder $b) => $b->whereValid($conversationId)->whereNotDeletedConversation())
                ->whereHas('conversation', fn(ConversationBuilder|BelongsTo $b) => $b->where('type', ConversationTypeEnum::MINE))
                ->findOrFail($messageId);
        }

        $remainingUsers = $this->checkRemainingUsers($conversationId, $message->id, $conversationMember);

        if ($this->shouldForceDeleteMessage($deleteForAll, $message, $conversationMember, $remainingUsers)) {
            if (! $remainingUsers) {
                $message->forceDelete();
                $this->broadcastConversationToLoggedUser($conversationId);
            } else {
                $message->delete();
                $this->broadcastDeletedMessage($conversationId, $messageId);
            }
        } else {
            $message->deletedMessages()->create(['conversation_member_id' => $conversationMember->id]);
            $this->broadcastConversationToLoggedUser($conversationId);
        }
    }

    protected function findConversation($conversationId, $excludeId = null)
    {
        return Conversation::query()
            ->when(! is_null($excludeId), fn($q) => $q->where('id', '<>', $excludeId))
            ->findOrFail($conversationId);
    }

    /**
     * @throws ValidationErrorsException
     */
    protected function validateParentMessage($data, $conversation)
    {
        if (isset($data['parent_id'])) {
            $parentMessage = $this->getParentMessage($conversation, $data['parent_id']);

            if (! $parentMessage) {
                throw new ValidationErrorsException([
                    'parent_id' => translate_error_message('message', 'not_exists'),
                ]);
            }
        }
    }

    protected function getParentMessage($conversation, $parentId)
    {
        return $conversation
            ->messages()
            ->whereValid($conversation->id)
            ->find($parentId);
    }

    protected function findConversationMember($conversation)
    {
        return ConversationMemberHelper::getCurrentMember($conversation->id);
    }

    protected function createMessage($data, $conversation, $member)
    {
        return $conversation->messages()->create($data + [
            'conversation_member_id' => $member->id,
        ]);
    }

    protected function handleMedia($message, $data)
    {
        if (isset($data['location_media'])) {
            $this->fileOperationService->addMedia($message, $data['location_media'], 'chat_location_media');
        }

        if (isset($data['media'])) {
            $file = request()->file('media');

            //            if (
            //                in_array($message->type, [MessageTypeEnum::AUDIO, MessageTypeEnum::RECORD])
            //            ) {
            //                $name = Str::random();
            //                $ffmpeg = FFMpeg::create();
            //                $audio = $ffmpeg->open($file->getPathname());
            //                $audioPath = storage_path("app/public/$name.mp3");
            //                $audio->save(new Mp3, $audioPath);
            //
            //                $message->addMedia($audioPath)
            //                    ->preservingOriginal()
            //                    ->toMediaCollection('chat_media');
            //
            //                if (file_exists($audioPath)) {
            //                    unlink($audioPath);
            //                }
            //
            //                return;
            //            }
            $this->fileOperationService->addMedia($message, $data['media'], 'chat_media', request()->file('media')->clientExtension());
        }
    }

    protected function checkRemainingUsers($conversationId, $messageId, $conversationMember)
    {
        return ConversationMember::query()
            ->whereDoesntHave('deletedMessages', function ($q) use ($messageId) {
                $q->where('conversation_message_id', $messageId);
            })
            ->where('member_id', '<>', $conversationMember->member_id)
            ->where('conversation_id', $conversationId)
            ->exists();
    }

    protected function shouldForceDeleteMessage($deleteForAll, $message, $conversationMember, $remainingUsers)
    {
        return ($deleteForAll && $message->conversation_member_id == $conversationMember->id) || ! $remainingUsers;
    }

    protected function replicateMessage($message, $targetMember, $targetConversationId)
    {
        $newMessage = $message->replicate(['conversation_member_id', 'parent_id', 'conversation_id', 'seen']);
        $newMessage->conversation_member_id = $targetMember->id;
        $newMessage->conversation_id = $targetConversationId;
        $newMessage->forwarded = true;
        $newMessage->save();

        $message->media->each(function (Media $media) use ($newMessage) {
            $newMessage->addMedia($media->getPath())
                ->preservingOriginal()
                ->toMediaCollection($media->collection_name);
        });

        return $newMessage;
    }

    public function broadcastCreatedMessage($conversationId, $message)
    {
        $conversation = $this->getConversationWithLatestDetails($conversationId);
        $this->broadcastToUsers($conversation, $message);
    }

    protected function getConversationWithLatestDetails($conversationId)
    {
        return $this->conversationService->show($conversationId);
    }

    protected function broadcastToUsers(Conversation $conversation, $message)
    {
        if ($conversation->type == ConversationTypeEnum::MINE) {
            broadcast(new NewMessageEvent($conversation->id, $message))->toOthers();
            broadcast(new ConversationUpdatedEvent(auth()->id(), $conversation));
        } else {
            $conversationUsers = $conversation->users()->select(['users.id'])->get();
            broadcast(new NewMessageEvent($conversation->id, $message))->toOthers();
            dispatch(new BulkConversationsBroadcast($conversation->id, $conversationUsers, app(ConversationService::class)));
        }
    }

    protected function broadcastDeletedMessage($conversation, $messageId)
    {
        $conversation = $conversation instanceof Conversation
            ? $conversation
            : $this->getConversationWithLatestDetails($conversation);

        $conversationUsers = $conversation->users()->select(['users.id'])->get();

        event(new MessageDeletedEvent($conversation->id, $messageId));
        dispatch(new BulkConversationsBroadcast($conversation->id, $conversationUsers, app(ConversationService::class)));
    }

    public static function prepareMessages(LengthAwarePaginator $paginatedData): LengthAwarePaginator
    {
        $collection = self::prepareMessagesCollection($paginatedData->getCollection());

        return new LengthAwarePaginator($collection, $paginatedData->total(), $paginatedData->perPage(), $paginatedData->currentPage());
    }

    public static function prepareMessagesCollection(Collection|\Illuminate\Support\Collection $collection)
    {
        return $collection->map(fn($message) => self::prepareMessage($message));
    }

    public static function prepareMessage(ConversationMessage $message): ConversationMessage
    {
        $message->setAttribute('page', PaginationHelper::calculatePageByPosition($message->position));

        return $message;
    }

    private function getBasicMessage($conversation, $message)
    {
        return ConversationMessage::query()
            ->when(true, fn(ConversationMessageBuilder $b) => $b->whereValid($conversation))
            ->whereNotDeletedConversation()
            ->findOrFail($message);
    }

    private function broadcastConversationToLoggedUser($conversationId)
    {
        $conversation = $this->getConversationWithLatestDetails($conversationId);
        event(new ConversationUpdatedEvent(auth()->id(), $conversation));
    }

    public static function notifyUser(
        Conversation $conversation,
        ConversationMessage $message,
        bool $isPin = false,
        bool $isReaction = false,

    ): void {
        $member = $conversation->members()->where('member_id', '<>', auth()->id())->first();

        if ($member) {
            $message->member->setRelation('user', (new ConversationService)->getOtherUser(auth()->id()));

            Notification::send(
                $member->user,
                new FcmNotification(
                    'message_title',
                    $message->content ?: 'attachment',
                    additionalData: [
                        'type' => NotificationTypeEnum::CHAT,
                        'conversation_id' => $conversation->id,
                        'is_pin' => $isPin,
                        'is_reaction' => $isReaction,
                        'message' => json_encode(collect(ConversationMessageResource::make($message))->toArray()),
                    ],
                    viaChannels: [FcmChannel::class],
                    shouldTranslate: [
                        'title' => true,
                    ],
                    translatedAttributes: [
                        'title' => [
                            'ticketId' => $message->ticket_id,
                        ]
                    ]
                )
            );
        }
    }
}
