<?php

namespace Modules\Chat\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Database\Factories\ConversationMessageActionFactory;

class ConversationMessageAction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ConversationMessageActionFactory
    {
        //return ConversationMessageActionFactory::new();
    }
}
