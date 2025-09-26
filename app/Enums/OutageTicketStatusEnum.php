<?php

namespace App\Enums;

enum OutageTicketStatusEnum: int
{
    case OPEN = 0;
    case CLOSED = 1;

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        ];
    }
}
