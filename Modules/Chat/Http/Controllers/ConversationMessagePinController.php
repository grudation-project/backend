<?php

namespace Modules\Chat\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Chat\Http\Requests\MessagePinRequest;
use Modules\Chat\Services\ConversationMessagePinService;
use Modules\Chat\Services\ConversationMessageService;
use Modules\Chat\Transformers\ConversationMessageResource;

class ConversationMessagePinController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ConversationMessagePinService $conversationMessagePinService) {}

    public function index($conversation)
    {
        $pinnedMessages = $this->conversationMessagePinService->index($conversation);

        $pinnedMessages = ConversationMessageService::prepareMessagesCollection($pinnedMessages);

        return $this->resourceResponse(ConversationMessageResource::collection($pinnedMessages));
    }

    public function pin(MessagePinRequest $request, $conversation, $id)
    {
        $this->conversationMessagePinService->pin($request->validated(), $conversation, $id);

        return $this->okResponse(message: translate_word($request->pin ? 'message_pinned' : 'message_unpinned'));
    }
}
