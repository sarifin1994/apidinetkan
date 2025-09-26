<?php

namespace App\Enums;

enum TransactionCategoryEnum: int
{
    case INVOICE = 1;
    case HOTSPOT = 2;
    case NEW_CLIENT = 3;
    case OPERATIONAL = 4;
    case BELANJA = 5;
    case PARTNER_FEE = 6;
    case OTHER = 7;
    case LICENSE = 8;

    public function label(): string
    {
        return match ($this) {
            self::INVOICE => 'Invoice',
            self::HOTSPOT => 'Hotspot',
            self::NEW_CLIENT => 'New Client',
            self::OPERATIONAL => 'Operational',
            self::BELANJA => 'Belanja',
            self::PARTNER_FEE => 'Partner Fee',
            self::OTHER => 'Other',
            self::LICENSE => 'License',
        };
    }

    public static function getCategories(): array
    {
        return [
            self::INVOICE => 'Invoice',
            self::HOTSPOT => 'Hotspot',
            self::NEW_CLIENT => 'New Client',
            self::OPERATIONAL => 'Operational',
            self::BELANJA => 'Belanja',
            self::PARTNER_FEE => 'Partner Fee',
            self::OTHER => 'Other',
            self::LICENSE => 'License',
        ];
    }

    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'Invoice' => self::INVOICE,
            'Hotspot' => self::HOTSPOT,
            'New Client' => self::NEW_CLIENT,
            'Operational' => self::OPERATIONAL,
            'Belanja' => self::BELANJA,
            'Partner Fee' => self::PARTNER_FEE,
            'Other' => self::OTHER,
            'License' => self::LICENSE,
        };
    }

    public static function fromValue(int $value): self
    {
        return match ($value) {
            1 => self::INVOICE,
            2 => self::HOTSPOT,
            3 => self::NEW_CLIENT,
            4 => self::OPERATIONAL,
            5 => self::BELANJA,
            6 => self::PARTNER_FEE,
            7 => self::OTHER,
            8 => self::LICENSE,
        };
    }
}
