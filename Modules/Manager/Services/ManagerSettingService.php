<?php

namespace Modules\Manager\Services;

use Modules\Manager\Models\ManagerSetting;
use Modules\Manager\Traits\ManagerSetter;

class ManagerSettingService
{
    use ManagerSetter;

    public function show()
    {
        return [
            'automatic_assignment' => $this->getManager()->automatic_assignment,
        ];
    }

    public function update(array $data)
    {
        $this->getManager()->update($data);

        return $this->show();
    }

    public static function isAutomaticAssignmentAllowed(ManagerSetting $settings)
    {
        return $settings->automatic_assignment;
    }
}
