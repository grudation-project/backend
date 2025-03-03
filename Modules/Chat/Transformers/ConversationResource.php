<?php

namespace Modules\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Modules\Auth\Transformers\UserResource;
use Modules\Markable\Transformers\ReactionResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->whenHas('type'),
            'pinned' => $this->whenHas('pinned'),
            'unseen_messages_count' => $this->whenHas('unseen_messages_count'),
            'other_user' => UserResource::make($this->whenLoaded('otherUser')),
            'latest_message' => ConversationMessageResource::make($this->whenLoaded('latestMessage')),
            'latest_reaction' => $this->when($this->relationLoaded('latestReaction') && $this->relationLoaded('latestMessage'), function () {
                if (! $this->latestReaction || ! $this->latestMessage) {
                    return null;
                }

                $latestMessageDate = Carbon::parse($this->latestMessage->created_at);
                $latestReactionDate = Carbon::parse($this->latestReaction->created_at);

                if ($latestReactionDate->isBefore($latestMessageDate)) {
                    return null;
                }

                return ReactionResource::make($this->latestReaction);
            }),
        ];
    }
}
