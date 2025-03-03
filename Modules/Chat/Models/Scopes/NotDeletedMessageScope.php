<?php

namespace Modules\Chat\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\Chat\Helpers\ConversationMemberHelper;

class NotDeletedMessageScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        //        $conversationId = request()->route('conversationId');
        //
        //        if(! is_null($conversationId)) {
        //            $userId = auth()->id();
        //            $member = ConversationMemberHelper::getCurrentMember($conversationId, $userId);
        //
        //            $builder->whereNotDeleted($conversationId, $conversationMember->id);
        //        }
    }
}
