<?php

namespace App\Enums;

enum LicenseLevelEnum: int
{
    case CLOUD_BRONZE = 1;
    case CLOUD_SILVER = 2;
    case CLOUD_GOLD = 3;
    case CLOUD_PLATINUM = 4;
    case CLOUD_DIAMOND = 5;
    case LOCAL_RADIUS = 6;

    public function label(): string
    {
        return match ($this) {
            self::CLOUD_BRONZE => 'Cloud Bronze',
            self::CLOUD_SILVER => 'Cloud Silver',
            self::CLOUD_GOLD => 'Cloud Gold',
            self::CLOUD_PLATINUM => 'Cloud Platinum',
            self::CLOUD_DIAMOND => 'Cloud Diamond',
            self::LOCAL_RADIUS => 'Local Radius',
        };
    }

    public static function getLevels(): array
    {
        return [
            self::CLOUD_BRONZE => 'Cloud Bronze',
            self::CLOUD_SILVER => 'Cloud Silver',
            self::CLOUD_GOLD => 'Cloud Gold',
            self::CLOUD_PLATINUM => 'Cloud Platinum',
            self::CLOUD_DIAMOND => 'Cloud Diamond',
            self::LOCAL_RADIUS => 'Local Radius',
        ];
    }
}
