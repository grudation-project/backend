<?php

namespace Modules\Technician\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Technician\Traits\TechnicianRelations;

class Technician extends Model
{
    use TechnicianRelations;

    protected $fillable = ['user_id', 'manager_id'];
}
