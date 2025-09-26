<?php

namespace App\Enums;

enum DinetkanInvoiceStatusEnum: int
{
case NEW = 0;
case UNPAID = 1;
case PAID = 2;
case CANCEL = 3;
case EXPIRED= 4;

    public function label(): string
{
    return match ($this) {
    self::NEW => 'New',
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
        self::NEW => 'New',
        self::UNPAID => 'Unpaid',
        self::PAID => 'Paid',
        self::CANCEL => 'Cancel',
        self::EXPIRED => 'Expired',
    ];
}
}
