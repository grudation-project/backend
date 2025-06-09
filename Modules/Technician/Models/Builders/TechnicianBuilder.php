<?php

namespace Modules\Technician\Models\Builders;

use App\Models\Builders\UserBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianBuilder extends Builder
{
    public function withUserDetails()
    {
        return $this->with(['user' => fn(UserBuilder|BelongsTo $b) => $b->withMinimalDetails()])->withSectionDetails();
    }

    public function withMinimalDetailsForManager()
    {
        return $this->withUserDetails();
    }

    public function withSectionDetails()
    {
        return $this->with('section.service');
    }
}
