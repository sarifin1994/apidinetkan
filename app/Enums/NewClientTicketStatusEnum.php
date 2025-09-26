<?php

namespace App\Enums;

enum NewClientTicketStatusEnum: int
{
    case PENDING = 0;
    case OPEN = 1;
    case CLOSED = 2;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::PENDING => 'Pending',
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        ];
    }
}
