<?php

namespace Modules\Technician\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Manager\Models\Manager;
use Modules\Service\Models\Section;
use Modules\Ticket\Enums\TicketStatusEnum;
use Modules\Ticket\Models\Ticket;

trait TechnicianRelations
{
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function liveTickets()
    {
        return $this->hasMany(Ticket::class, 'technician_id')->where('status', TicketStatusEnum::IN_PROGRESS);
    }

    public function liveOverdueTickets()
    {
        return $this
            ->liveTickets()
            ->whereRaw('TIMESTAMPDIFF(MINUTE, assigned_at, NOW()) > maximum_minutes');
    }
}
