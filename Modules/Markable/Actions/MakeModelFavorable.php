<?php

namespace Modules\Markable\Actions;

use Illuminate\Database\Eloquent\Builder;

class MakeModelFavorable
{
    public function handle(Builder $builder, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        $favoritesOnly = request('favorites_only') == 'yes';
        $withFavorites = request('with_favorites') == 'yes';

        return $builder
            ->when($favoritesOnly, fn ($query) => $query->whereHasFavorites($userId)->withFavorites($userId))
            ->when(! $favoritesOnly && $withFavorites, fn ($query) => $query->withFavorites($userId));
    }
}
