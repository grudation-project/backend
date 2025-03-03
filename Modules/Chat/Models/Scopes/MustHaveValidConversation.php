<?php

namespace Modules\Chat\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class MustHaveValidConversation implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('conversation');
    }
}
