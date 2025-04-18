<?php

namespace Modules\Auth\Traits;

use App\Helpers\MediaHelper;
use Modules\Auth\Enums\AuthEnum;
use Modules\Manager\Models\Manager;
use Modules\Technician\Models\Technician;

trait UserRelations
{
    public function avatar()
    {
        return MediaHelper::mediaRelationship($this, AuthEnum::AVATAR_COLLECTION_NAME);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function technician()
    {
        return $this->hasOne(Technician::class);
    }
}
