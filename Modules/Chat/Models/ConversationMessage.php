<?php

namespace Modules\Chat\Models;

use App\Traits\PaginationTrait;
use App\Traits\Searchable;
use Elattar\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Chat\Models\Builders\ConversationMessageBuilder;
use Modules\Chat\Models\Scopes\MustHaveValidConversation;
use Modules\Chat\Traits\ConversationMessageRelations;
use Modules\Markable\Entities\ReactionModel;
use Modules\Markable\Traits\Markable;
use Modules\Markable\Traits\UserReactionRelations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ConversationMessage extends Model implements HasMedia
{
    use ConversationMessageRelations,
        HasUuids,
        InteractsWithMedia,
        Markable,
        PaginationTrait,
        Searchable,
        SoftDeletes,
        SpatialTrait,
        UserReactionRelations;

    protected $fillable = [
        'conversation_id',
        'conversation_member_id',
        'type',
        'delivered',
        'seen',
        'record_duration',
        'location',
        'content',
        'parent_id',
        'forwarded',
        'seen',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'delivered' => 'boolean',
        'forwarded' => 'boolean',
        'seen' => 'boolean',
        'pinned_till' => 'datetime',
    ];

    protected $spatialFields = [
        'location',
    ];

    protected static $marks = [
        ReactionModel::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(MustHaveValidConversation::class);
    }

    public function newEloquentBuilder($query): ConversationMessageBuilder
    {
        return new ConversationMessageBuilder($query);
    }

    public function pinnedTill(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ! is_null($value) && (Carbon::parse($value))->isFuture() ? Carbon::parse($value) : null,
        );
    }
}
