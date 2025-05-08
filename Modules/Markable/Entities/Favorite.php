<?php

namespace Modules\Markable\Entities;

use Modules\Markable\Abstracts\Mark;

class Favorite extends Mark
{
    public static function markableRelationName(): string
    {
        return 'favoriters';
    }
}
