<?php

namespace Modules\Service\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->whenHas('name'),
            'service_id' => $this->whenHas('service_id'),
            'service' => ServiceResource::make($this->whenLoaded('service')),
        ];
    }
}
