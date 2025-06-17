<?php

namespace Modules\Ticket\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Modules\Auth\Transformers\UserResource;
use Modules\Manager\Transformers\ManagerResource;
use Modules\Service\Transformers\SectionResource;
use Modules\Service\Transformers\ServiceResource;
use Modules\Technician\Transformers\TechnicianResource;
use Modules\Ticket\Enums\TicketStatusEnum;

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
            'created_at' => $this->whenHas('created_at'),
            'assigned_at' => $this->whenHas('assigned_at'),
            'maximum_minutes' => $this->whenHas('maximum_minutes'),
            'is_overdue' => $this->when(!is_null($this->maximum_minutes), function () {
                if (is_null($this->assigned_at) || $this->status != TicketStatusEnum::IN_PROGRESS) {
                    return false;
                }

                $date = Carbon::parse($this->assigned_at)->addMinutes($this->maximum_minutes);

                return $date->isPast();
            }),
            'description' => $this->whenHas('description'),
            'service' => $this->whenLoaded('service', function () {
                return ServiceResource::make($this->service);
            }),
            'section' => $this->whenLoaded('section', function () {
                return SectionResource::make($this->section);
            }),
            'user' => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return ManagerResource::make($this->manager);
            }),
            'technician' => $this->whenLoaded('technician', function () {
                return TechnicianResource::make($this->technician);
            }),
        ];
    }
}
