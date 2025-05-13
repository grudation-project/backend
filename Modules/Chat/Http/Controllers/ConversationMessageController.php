<?php

namespace Modules\Chat\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Notification;
use Modules\Chat\Http\Requests\ForwardMessageRequest;
use Modules\Chat\Http\Requests\MessageRequest;
use Modules\Chat\Services\ConversationMessageService;
use Modules\Chat\Transformers\ConversationMessageResource;
use Modules\FcmNotification\Enums\NotificationTypeEnum;
use Modules\FcmNotification\Notifications\FcmNotification;

class ConversationMessageController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ConversationMessageService $conversationMessageService) {}

    public function index($conversation)
    {
        $messages = $this->conversationMessageService->index($conversation);

        return $this->paginatedResponse($messages, ConversationMessageResource::class);
    }

    public function store(MessageRequest $request, $conversation)
    {
        [$message] = $this->conversationMessageService->store($request->validated(), $conversation);

        return $this->createdResponse(ConversationMessageResource::make($message));
    }

    public function destroy($conversationId, $id)
    {
        $this->conversationMessageService->destroy($conversationId, $id);

        return $this->okResponse(message: translate_success_message('message', 'deleted'));
    }

    public function forward(ForwardMessageRequest $request, $conversation, $id)
    {
        $message = $this->conversationMessageService->forward($request->validated(), $conversation, $id);

        return $this->createdResponse(ConversationMessageResource::make($message));
    }
}
