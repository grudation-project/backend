<?php

namespace Modules\Chat\Transformers;

use App\Helpers\ResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Transformers\UserResource;
use Modules\Map\Helpers\PointHelper;
use Modules\Markable\Transformers\ReactionResource;

class ConversationMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->whenHas('member_id'),
            'type' => $this->whenHas('type'),
            'deleted_for_all' => $this->whenHas('deleted_at', function () {
                return ! is_null($this->deleted_at);
            }),
            'forwarded' => $this->whenHas('forwarded'),
            'seen' => $this->whenHas('seen'),
            'created_at' => $this->whenHas('created_at'),
            $this->mergeWhen(! is_null($this->location), function () {
                return PointHelper::getFormattedLatLng($this->location);
            }),
            'media' => $this->whenLoaded('mediaSource', function () {
                return ResourceHelper::getFirstMediaOriginalUrl($this, 'mediaSource', shouldReturnDefault: false);
            }),
            'media_object' => $this->whenLoaded('mediaSource', function () {
                return ResourceHelper::getMediaFullObject($this, 'mediaSource', shouldReturnDefault: false);
            }),
            'record_duration' => $this->whenHas('record_duration'),
            'content' => $this->whenHas('content'),
            'page' => $this->whenHas('page'),
            'position' => $this->whenHas('position'),
            'pinned_till' => $this->whenHas('pinned_till'),
            'reactions' => UserResource::collection($this->whenLoaded('reactions')),
            'latest_reaction' => ReactionResource::make($this->whenLoaded('lastReaction')),
            'user' => $this->when($this->relationLoaded('member') && $this->member->relationLoaded('user'), function () {
                return UserResource::make($this->member->user);
            }),
            'parent_message' => ConversationMessageResource::make($this->whenLoaded('parentMessage')),
        ];
    }
}
