<?php

namespace App\Enums;

enum ServiceStatusEnum: int
{
    case NEW = 0;
    case ACTIVE = 1;
    case INACTIVE = 2;
    case OVERDUE = 3;
    case SUSPEND = 4;
    case PROGRESS = 5;
    case CANCEL = 6;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::OVERDUE => 'Overdue',
            self::SUSPEND => 'Suspend',
            self::PROGRESS => 'Progress / Installation',
            self::CANCEL => 'Cancel'
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::NEW->value => 'New',
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
            self::OVERDUE->value => 'Overdue',
            self::SUSPEND->value => 'Suspend',
            self::PROGRESS->value => 'Progress / Installation',
            self::CANCEL->value => 'Cancel'
        ];
    }

    public static function toArray(): array
    {
        return [
            self::NEW,
            self::ACTIVE,
            self::INACTIVE,
            self::OVERDUE,
            self::SUSPEND,
            self::PROGRESS,
            self::CANCEL
        ];
    }

    public static function getValues(): array
    {
        return [
            self::NEW->value,
            self::ACTIVE->value,
            self::INACTIVE->value,
            self::OVERDUE->value,
            self::SUSPEND->value,
            self::PROGRESS->value,
            self::CANCEL->value
        ];
    }
}
