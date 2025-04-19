<?php

namespace Modules\Technician\Traits;

use Modules\Technician\Exceptions\TechnicianException;
use Modules\Technician\Helpers\TechnicianHelper;
use Modules\Technician\Models\Technician;

trait TechnicianSetter
{
    private ?Technician $technician = null;

    public function setTechnician(Technician $technician): self
    {
        $this->technician = $technician;

        return $this;
    }

    /**
     * @throws TechnicianException
     */
    public function getTechnician(): ?Technician
    {
        return $this->technician ?: TechnicianHelper::getUserTechnician();
    }
}
