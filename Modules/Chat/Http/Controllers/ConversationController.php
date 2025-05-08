<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Transformers\UserResource;
use Modules\Chat\Http\Requests\ConversationPinRequest;
use Modules\Chat\Http\Requests\ConversationRequest;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Services\ConversationService;
use Modules\Chat\Transformers\ConversationResource;

class ConversationController extends Controller
{
    use HttpResponse;

    public function __construct(private readonly ConversationService $conversationService) {}

    public function index()
    {
        $conversations = $this->conversationService->index();

        return $this->resourceResponse(ConversationResource::collection($conversations));
    }

    public function store(ConversationRequest $request)
    {
        $conversation = $this->conversationService->store($request->validated());
        $conversation->setRelation(
            'otherUser',
            $this->conversationService->getOtherUser($request->user_id ?: auth()->id())
        );

        return $this->createdResponse(ConversationResource::make($conversation), message: translate_success_message('conversation', 'created'));
    }

    public function pin(ConversationPinRequest $request, $conversation)
    {
        $this->conversationService->pin($request->validated(), $conversation);

        return $this->okResponse(message: translate_word($request->pin ? 'conversation_pinned' : 'conversation_unpinned'), showToast: false);
    }

    public function getByUser()
    {
        $conversation = $this->conversationService->getByUser(request()->input('user_id'));

        if (! $conversation) {
            return $this->resourceResponse(null);
        }

        return $this->resourceResponse(ConversationResource::make($conversation));
    }

    public function otherUser()
    {
        $user = $this->conversationService->getOtherUser(request()->input('user_id'));

        return $this->resourceResponse(UserResource::make($user));
    }

    public function markAsSeen($conversationId): JsonResponse
    {
        $this->conversationService->markAsSeen($conversationId);

        return $this->okResponse(message: translate_word('conversation_seen'));
    }

    public function destroy($conversationId)
    {
        $this->conversationService->destroy($conversationId);

        return $this->okResponse(message: translate_success_message('conversation', 'deleted'));
    }

    public function unseenCount()
    {
        $totalCount = 0;
        Conversation::query()
            ->select(['id'])
            ->when(true, fn (ConversationBuilder $b) => $b->withUnseenMessagesCount())
            ->each(function ($i) use (&$totalCount) {
                $totalCount += $i->unseen_messages_count;
            });

        return $this->resourceResponse(['unseen_count' => $totalCount]);
    }
}
