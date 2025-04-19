<?php

namespace Modules\Ticket\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Transformers\UserResource;
use Modules\Manager\Transformers\ManagerResource;
use Modules\Service\Transformers\ServiceResource;
use Modules\Technician\Transformers\TechnicianResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->whenHas('title'),
            'status' => $this->whenHas('status'),
            'description' => $this->whenHas('description'),
            'service' => $this->whenLoaded('service', function(){
               return ServiceResource::make($this->service);
            }),
            'user' => $this->whenLoaded('user', function(){
                return UserResource::make($this->user);
            }),
            'manager' => $this->whenLoaded('manager', function(){
                return ManagerResource::make($this->manager);
            }),
            'technician' => $this->whenLoaded('technician', function(){
                return TechnicianResource::make($this->technician);
            }),
        ];
    }
}
