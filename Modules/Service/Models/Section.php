<?php

namespace Modules\Service\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use Searchable;

    protected $fillable = [
        'name',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
