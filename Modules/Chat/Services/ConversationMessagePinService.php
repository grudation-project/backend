<?php

namespace Modules\Chat\Services;

use App\Services\FileOperationService;
use Illuminate\Support\Facades\DB;
use Modules\Chat\Models\Builders\ConversationMessageBuilder;
use Modules\Chat\Models\ConversationMessage;

class ConversationMessagePinService
{
    public function index($conversation)
    {
        return ConversationMessage::query()
            ->when(true, fn (ConversationMessageBuilder $b) => $b->whereValidPinned()->whereNotDeletedConversation())
            ->latest('pinned_till')
            ->where('conversation_id', $conversation)
            ->when(true, fn (ConversationMessageBuilder $b) => $b->withMessageDetails($conversation))
            ->get();
    }

    public function pin(array $data, $conversation, $id)
    {
        $message = ConversationMessage::query()
            ->when(true, fn (ConversationMessageBuilder $b) => $b->whereNotDeletedConversation())
            ->where('conversation_id', $conversation)
            ->findOrFail($id);

        DB::transaction(function () use ($message, $conversation, $data) {
            $shouldPin = $data['pin'];

            if ($shouldPin) {
                $pinnedMessages = ConversationMessage::query()
                    ->when(true, fn (ConversationMessageBuilder $b) => $b->whereNotDeletedConversation())
                    ->where('conversation_id', $conversation)
                    ->when(true, fn (ConversationMessageBuilder $b) => $b->whereValidPinned())
                    ->oldest('pinned_till')
                    ->get(['id', 'pinned_till']);

                if ($pinnedMessages->count() == 3) {
                    $pinnedMessages->first()->forceFill(['pinned_till' => null])->save();
                }
            }

            $message->forceFill(['pinned_till' => $shouldPin ? now()->addDays($data['pinned_till']) : null])->save();
        });

        if ($data['pin']) {
            ConversationMessageService::notifyUser(
                $message->conversation,
                (
                new ConversationMessageService(
                    app(FileOperationService::class),
                    app(ConversationService::class)
                )
                )->show($conversation, $message->id),
                true
            );
        }
    }
}
