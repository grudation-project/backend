<?php

namespace Modules\Markable\Traits;

use Modules\Markable\Actions\MakeModelFavorable;
use Modules\Markable\Helpers\FavoriteHelper;

trait Favorable
{
    public function whereHasFavorites($userId = null): static
    {
        return $this->whereHas(FavoriteHelper::RELATIONSHIP_NAME, function ($query) use ($userId) {
            $userId = $userId ?: auth()->id();

            $query->whereUserId($userId);
        });
    }

    public function withFavorites($userId = null): static
    {
        return $this->with([FavoriteHelper::RELATIONSHIP_NAME => function ($query) use ($userId) {
            $userId = $userId ?: auth()->id();

            $query->whereUserId($userId);
        }]);
    }

    public function getFavorites($userId = null): static
    {
        return (new MakeModelFavorable)->handle($this, $userId);
    }

    public function withFavoritesCount(): static
    {
        return $this->withCount(FavoriteHelper::RELATIONSHIP_NAME);
    }
}
