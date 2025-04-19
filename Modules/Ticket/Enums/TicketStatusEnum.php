<?php

namespace Modules\Ticket\Enums;

enum TicketStatusEnum
{
    const PENDING = 0;
    const IN_PROGRESS = 1;
    const RESOLVED = 2;
    const CLOSED = 3;

    public static function toArray()
    {
        return [
            self::PENDING,
            self::IN_PROGRESS,
            self::RESOLVED,
            self::CLOSED,
        ];
    }
}
