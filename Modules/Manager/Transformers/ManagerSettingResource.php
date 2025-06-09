<?php

namespace Modules\Manager\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'allowed_late_minutes' => $this->whenHas('allowed_late_minutes'),
            'automatic_assignment' => $this->whenHas('automatic_assignment'),
        ];
    }
}
