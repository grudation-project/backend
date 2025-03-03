<?php

namespace Modules\Chat\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\Chat\Models\Builders\ConversationBuilder;

class MyConversationScope implements Scope
{
    public function apply(ConversationBuilder|Builder $builder, Model $model)
    {
        return $builder->whereMember();
    }
}
