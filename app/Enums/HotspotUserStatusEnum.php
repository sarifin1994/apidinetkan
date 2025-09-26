<?php

namespace App\Enums;

enum HotspotUserStatusEnum: int
{
    case DISABLED = 0;
    case NEW = 1;
    case ACTIVE = 2;
    case EXPIRED = 3;

    public function label(): string
    {
        return match ($this) {
            self::DISABLED => 'Disabled',
            self::NEW => 'New',
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::DISABLED => 'Disabled',
            self::NEW => 'New',
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
        ];
    }
}
