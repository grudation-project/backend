<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Models\Builders\DeletedMessageBuilder;

class DeletedMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_member_id',
        'conversation_message_id',
    ];

    public function newEloquentBuilder($query): DeletedMessageBuilder
    {
        return new DeletedMessageBuilder($query);
    }

    public function conversationMember()
    {
        return $this->belongsTo(ConversationMember::class);
    }
}
