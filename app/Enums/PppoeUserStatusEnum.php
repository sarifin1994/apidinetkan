<?php

namespace App\Enums;

enum PppoeUserStatusEnum: int
{
    case SUSPENDED = 2;

    public function label(): string
    {
        return match ($this) {
            self::SUSPENDED => 'Suspended',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::SUSPENDED => 'Suspended',
        ];
    }
}
