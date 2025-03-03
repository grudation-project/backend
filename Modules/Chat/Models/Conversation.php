<?php

namespace Modules\Chat\Models;

use App\Traits\PaginationTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Models\Builders\ConversationBuilder;
use Modules\Chat\Models\Scopes\MyConversationScope;
use Modules\Chat\Traits\ConversationRelations;

class Conversation extends Model
{
    use ConversationRelations, HasUuids, PaginationTrait;

    protected $fillable = ['type', 'pinned'];

    protected $casts = [
        'latest_message_time' => 'datetime',
        'pinned' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(MyConversationScope::class);
    }

    public function newEloquentBuilder($query): ConversationBuilder
    {
        return new ConversationBuilder($query);
    }
}
