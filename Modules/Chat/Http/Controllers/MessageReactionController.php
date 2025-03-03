<?php

namespace Modules\Chat\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Routing\Controller;
use Modules\Chat\Actions\ConversationMessageReactAction;
use Modules\Chat\Transformers\ConversationMessageResource;
use Modules\Markable\Http\Requests\ReactionRequest;

class MessageReactionController extends Controller
{
    use HttpResponse;

    public function __invoke(ReactionRequest $request, $conversation, $id, ConversationMessageReactAction $messageReactAction)
    {
        $message = $messageReactAction->handle($request->validated(), $conversation, $id);

        return $this->resourceResponse(ConversationMessageResource::make($message));
    }
}
