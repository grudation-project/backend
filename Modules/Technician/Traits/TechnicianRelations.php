<?php

namespace Modules\Technician\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Manager\Models\Manager;

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
}
