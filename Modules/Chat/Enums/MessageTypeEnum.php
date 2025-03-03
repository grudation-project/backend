<?php

namespace Modules\Chat\Enums;

enum MessageTypeEnum
{
    const TEXT = 0;

    const IMAGE = 1;

    const VIDEO = 2;

    const AUDIO = 3;

    const RECORD = 4;

    const DOCUMENT = 5;

    const LOCATION = 6;

    public static function toArray(): array
    {
        return [
            self::TEXT,
            self::IMAGE,
            self::VIDEO,
            self::AUDIO,
            self::RECORD,
            self::DOCUMENT,
            self::LOCATION,
        ];
    }
}
