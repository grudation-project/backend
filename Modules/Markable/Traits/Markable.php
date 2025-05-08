<?php

namespace Modules\Markable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Markable\Abstracts\Mark;
use Modules\Markable\Entities\Scopes\MarkableScope;
use Modules\Markable\Exceptions\InvalidMarkInstanceException;

trait Markable
{
    /**
     * @throws InvalidMarkInstanceException
     */
    public static function bootMarkable(): void
    {
        static::registerMarks();

        static::addGlobalScope(new MarkableScope);

        static::deleting(
            /**
             * @throws InvalidMarkInstanceException
             */ fn ($markable) => self::deleteMarks($markable)
        );
    }

    public static function marks(): array
    {
        return static::$marks ?? [];
    }

    public function scopeWhereHasMark(Builder $builder, Mark $mark, Model $user, ?string $value = null): Builder
    {
        return $builder->whereHas(
            $mark->markableRelationName(),
            fn (Builder $b) => $b->where([
                $mark->getQualifiedUserIdColumn() => $user->getKey(),
                'value' => $value,
            ])
        );
    }

    /**
     * @throws InvalidMarkInstanceException
     */
    protected static function deleteMarks(self $markable): void
    {
        foreach (static::marks() as $markClass) {
            $markModel = self::getMarkModelInstance($markClass);

            $markRelationName = $markModel->markRelationName();

            $markable->$markRelationName()->delete();
        }
    }

    /**
     * @throws InvalidMarkInstanceException
     */
    protected static function registerMarks(): void
    {
        foreach (static::marks() as $markClass) {
            static::addMarkableRelation($markClass);
        }
    }

    /**
     * @throws InvalidMarkInstanceException
     */
    protected static function addMarkableRelation(string $markClass): void
    {
        $markModel = self::getMarkModelInstance($markClass);

        static::resolveRelationUsing(
            $markModel->markableRelationName(),
            fn ($markable) => $markable
                ->morphToMany(config('markable.user_model'), 'markable', $markModel->getTable())
                ->using($markModel->getMorphClass())
                ->withPivot('value')
                ->withTimestamps()
        );

        static::resolveRelationUsing(
            $markModel->markRelationName(),
            fn ($markable) => $markable
                ->morphMany($markClass, 'markable')
        );
    }

    /**
     * @throws InvalidMarkInstanceException
     */
    protected static function getMarkModelInstance(string $markClass): Mark
    {
        $instance = new $markClass;

        if (! $instance instanceof Mark) {
            throw InvalidMarkInstanceException::create();
        }

        return $instance;
    }
}
