<?php

namespace App\Enums;

enum InvoiceStatusEnum: int
{
    case UNPAID = 0;
    case PAID = 1;
    case CANCEL = 2;
    case EXPIRED= 3;

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::CANCEL => 'Cancel',
            self::EXPIRED => 'Expired',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public static function getStatuses(): array
    {
        return [
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::CANCEL => 'Cancel',
            self::EXPIRED => 'Expired',
        ];
    }
}
