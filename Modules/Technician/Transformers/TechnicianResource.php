<?php

namespace Modules\Technician\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Transformers\UserResource;
use Modules\Service\Transformers\SectionResource;

class TechnicianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section' => SectionResource::make($this->whenLoaded('section')),
            'user' => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            })
        ];
    }
}
