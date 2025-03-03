<?php

namespace Modules\Chat\Actions;

use App\Services\FileOperationService;
use Modules\Chat\Models\Builders\ConversationMessageBuilder;
use Modules\Chat\Models\ConversationMessage;
use Modules\Chat\Services\ConversationMessageService;
use Modules\Chat\Services\ConversationService;
use Modules\Markable\Entities\ReactionModel;

readonly class ConversationMessageReactAction
{
    public function __construct(private ConversationMessageService $conversationMessageService) {}

    public function handle(array $data, $conversation, $id)
    {
        ConversationMessage::query()
            ->when(true, fn (ConversationMessageBuilder $q) => $q->whereValid($conversation)->whereNotDeletedConversation())
            ->findOrFail($id);

        $messageReaction = ReactionModel::query()
            ->where('user_id', auth()->id())
            ->whereIsMessage($id)
            ->first();
        $shouldSendNotification = false;

        if ($data['remove']) {
            $messageReaction?->delete();
        } else {
            if ($messageReaction) {
                $messageReaction->forceFill(['value' => $data['value']]);

                if ($messageReaction->isDirty('value')) {
                    $messageReaction->save();
                    $shouldSendNotification = $messageReaction->wasChanged('value');
                }

            } else {
                ReactionModel::query()->create([
                    'markable_id' => $id,
                    'markable_type' => ConversationMessage::class,
                    'value' => $data['value'],
                    'user_id' => auth()->id(),
                ]);

                $shouldSendNotification = true;
            }
        }

        $message = $this->conversationMessageService->show($conversation, $id);

        $this->conversationMessageService->broadcastCreatedMessage($conversation, $message);

        if ($shouldSendNotification) {
            ConversationMessageService::notifyUser(
                $message->conversation,
                (
                new ConversationMessageService(
                    app(FileOperationService::class),
                    app(ConversationService::class)
                )
                )
                    ->show($conversation, $message->id),
                isReaction: true,
            );
        }

        return $message;
    }
}
