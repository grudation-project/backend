<?php

namespace Modules\Markable\Entities;

use App\Traits\PaginationTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Modules\Markable\Abstracts\Mark;
use Modules\Markable\Helpers\FavoriteHelper;

class FavoriteModel extends Mark
{
    use PaginationTrait, HasUuids;

    public $table = 'markable_favorites';

    public static function markableRelationName(): string
    {
        return FavoriteHelper::RELATIONSHIP_NAME;
    }
}
