<?php

namespace Modules\Manager\Traits;

use Modules\Manager\Helpers\ManagerHelper;
use Modules\Manager\Models\Manager;

trait ManagerSetter
{
    private ?Manager $manager = null;

    public function getManager()
    {
        return $this->manager ?: ManagerHelper::getUserManager();
    }
}
