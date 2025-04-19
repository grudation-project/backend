<?php

namespace Modules\Ticket\Models;

use App\Models\User;
use App\Traits\PaginationTrait;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Modules\Manager\Models\Manager;
use Modules\Service\Models\Service;
use Modules\Technician\Models\Technician;
use Modules\Ticket\Models\Builders\TicketBuilder;

class Ticket extends Model
{
    use PaginationTrait, Searchable;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'service_id',
        'manager_id',
        'technician_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function newEloquentBuilder($query)
    {
        return new TicketBuilder($query);
    }
}
