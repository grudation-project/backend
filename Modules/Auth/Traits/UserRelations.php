<?php

namespace Modules\Auth\Traits;

use App\Helpers\MediaHelper;
use Modules\Auth\Enums\AuthEnum;

trait UserRelations
{
    public function avatar()
    {
        return MediaHelper::mediaRelationship($this, AuthEnum::AVATAR_COLLECTION_NAME);
    }
}
