<?php

namespace App\Enums;

enum UserDinetStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case NEW = 2;
    case OVERDUE = 3;

    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Unknown',
            self::ACTIVE => 'Active',
            self::NEW => 'New',
            self::OVERDUE => 'Overdue',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::INACTIVE => 'Unknown',
            self::ACTIVE => 'Active',
            self::NEW => 'New',
            self::OVERDUE => 'Overdue',
        ];
    }

    public static function toArray(): array
    {
        return [
            self::INACTIVE,
            self::ACTIVE,
            self::NEW,
            self::OVERDUE,
        ];
    }

    public static function getValues(): array
    {
        return [
            self::INACTIVE->value,
            self::ACTIVE->value,
            self::NEW->value,
            self::OVERDUE->value,
        ];
    }
}
