<?php

namespace Modules\Chat\Enums;

enum ConversationTypeEnum
{
    const PRIVATE = 0;

    const GROUP = 3;

    const MINE = 1;

    public static function toArray(): array
    {
        return [
            self::PRIVATE,
            //            self::GROUP,
            self::MINE,
        ];
    }
}
