<?php

namespace Modules\Service\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Manager\Models\Manager;

class Service extends Model
{
    use Searchable;

    protected $fillable = [
        'name',
    ];

    public function manager(): HasOne
    {
        return $this->hasOne(Manager::class);
    }

    public function scopeOnlyUnique($query, $ignoredId = null) {
        return $query
            ->whereDoesntHave('manager', fn($q) => $q->when(!is_null($ignoredId), fn($q2) => $q2->where('managers.service_id', '!=', $ignoredId)));
    }
}
