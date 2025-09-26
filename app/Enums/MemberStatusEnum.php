<?php

namespace App\Enums;

enum MemberStatusEnum: string
{
    case INACTIVE = 'inactive';
    case ACTIVE = 'active';

    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
        ];
    }

    public static function toArray(): array
    {
        return [
            self::INACTIVE,
            self::ACTIVE,
        ];
    }

    public static function getValues(): array
    {
        return [
            self::INACTIVE->value,
            self::ACTIVE->value,
        ];
    }
}
