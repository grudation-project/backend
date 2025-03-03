<?php

namespace Modules\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationMember extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['member_id'];

    public function deletedMessages()
    {
        return $this->hasMany(DeletedMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
