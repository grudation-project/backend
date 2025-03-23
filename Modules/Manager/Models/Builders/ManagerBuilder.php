<?php

namespace Modules\Manager\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class ManagerBuilder extends Builder
{
    public function withMinimalDetailsForAdmin(): ManagerBuilder
    {
        return $this->withServiceDetails()->withUserDetails();
    }

    public function withServiceDetails()
    {
        return $this->with('service:id,name');
    }

    public function withUserDetails()
    {
        return $this->with('user:id,name,email');
    }
}
