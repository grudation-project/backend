<?php

namespace Modules\Manager\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Transformers\UserResource;
use Modules\Service\Transformers\ServiceResource;

class ManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'automatic_assignment' => $this->whenHas('automatic_assignment'),
            'service' => $this->whenLoaded('service', function () {
                return ServiceResource::make($this->service);
            }),
            'user' => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
        ];
    }
}
