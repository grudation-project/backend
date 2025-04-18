<?php

namespace Modules\Technician\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Modules\Technician\Models\Builders\TechnicianBuilder;
use Modules\Technician\Traits\TechnicianRelations;

class Technician extends Model
{
    use TechnicianRelations, Searchable;

    protected $fillable = ['user_id', 'manager_id'];

    public function newEloquentBuilder($query)
    {
        return new TechnicianBuilder($query);
    }
}
