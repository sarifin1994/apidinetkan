<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case DISABLED = 0;
    case ACTIVE = 1;
    case SUSPEND = 2;
    case EXPIRED = 3;
    case NEW = 4;
    case ACCEPT = 5;

    public function label(): string
    {
        return match ($this) {
            self::DISABLED => 'DISABLED',
            self::ACTIVE => 'ACTIVE',
            self::SUSPEND => 'SUSPEND',
            self::EXPIRED => 'EXPIRED',
            self::NEW => 'NEW',
            self::ACCEPT => 'ACCEPT'
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::DISABLED => 'DISABLED',
            self::ACTIVE => 'ACTIVE',
            self::SUSPEND => 'SUSPEND',
            self::EXPIRED => 'EXPIRED',
            self::NEW => 'NEW',
            self::ACCEPT => 'ACCEPT'
        ];
    }

    public static function toArray(): array
    {
        return [
            self::DISABLED ,
            self::ACTIVE ,
            self::SUSPEND ,
            self::EXPIRED ,
            self::NEW ,
            self::ACCEPT
        ];
    }

    public static function getValues(): array
    {
        return [
            self::DISABLED->value ,
            self::ACTIVE->value ,
            self::SUSPEND->value ,
            self::EXPIRED->value,
            self::NEW->value ,
            self::ACCEPT->value
        ];
    }
}
