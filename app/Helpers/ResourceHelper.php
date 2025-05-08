<?php

namespace App\Helpers;

class ResourceHelper
{
    public static function getFirstMediaOriginalUrl($object, string $relationName = 'avatar', string $defaultImageName = 'store.png', bool $shouldReturnDefault = true)
    {
        if($object->relationLoaded($relationName))
        {
            return collect($object->getRelation($relationName)->first())->get('original_url') ?: ($shouldReturnDefault ? asset('/storage/default/store.png') : null);
        }

        return null;
    }

    public static function getMedia($collectionName, $thisValue, string $relationName = 'mediaPaths', string $defaultFile = 'store.png', bool $shouldReturnDefaultMedia = true)
    {
        $media = $thisValue->{$relationName}[
        $thisValue->{$relationName}->search(fn ($item) => $item->collection_name == $collectionName)
        ]
            ->original_url ?? null;

        return $media ?: ($shouldReturnDefaultMedia ? asset('/storage/default/store.png') : null);
    }

    public static function getImagesObject($object, string $relationName, string $defaultFileName = 'store.png', bool $shouldReturnDefault = true)
    {
        if ($object->relationLoaded($relationName)) {
            $images = [];
            $imagesRelation = $object->getRelation($relationName);

            $imagesRelation->map(
                function ($file) use (&$images) {
                    $images[] = ['id' => $file->id, 'url' => $file->original_url];
                }
            );

            return $images;
        }

        return null;
    }

    public static function shouldReturnForeignKey(
        $resource,
        string $relationName,
        string $foreignKey,
        bool $returnIfNull = false
    ) {
        return ! $resource->relationLoaded($relationName)
            && (
                $returnIfNull || ! is_null($resource->{$foreignKey})
            );
    }

    public static function getMediaFullObject($object, string $relationName, string $defaultFileName = 'store.png', bool $shouldReturnDefault = true)
    {
        if ($object->relationLoaded($relationName)) {
            $media = $object->getRelation($relationName)->first();

            return $media ? [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->original_url,
                'size' => $media->size,
            ] : ($shouldReturnDefault ? [
                'id' => 0,
                'name' => null,
                'url' => asset('/storage/default/store.png'),
                'size' => 0,
            ] : null);
        }

        return null;
    }
}
