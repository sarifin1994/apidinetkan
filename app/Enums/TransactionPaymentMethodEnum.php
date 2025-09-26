<?php

namespace App\Enums;

enum TransactionPaymentMethodEnum: int
{
    case CASH = 1;
    case TRANSFER = 2;

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::TRANSFER => 'Transfer',
        };
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::CASH => 'Cash',
            self::TRANSFER => 'Transfer',
        ];
    }
}
