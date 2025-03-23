<?php

namespace Modules\Manager\Models;

use App\Models\User;
use App\Traits\PaginationTrait;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Modules\Manager\Models\Builders\ManagerBuilder;
use Modules\Service\Models\Service;

class Manager extends Model
{
    use PaginationTrait, Searchable;

    protected $fillable = ['user_id', 'service_id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newEloquentBuilder($query)
    {
        return new ManagerBuilder($query);
    }
}
