<?php

namespace App\Enums;

enum TransactionTypeEnum: int
{
    case INCOME = 1;
    case EXPENSE = 2;

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
        };
    }

    public static function getTypes(): array
    {
        return [
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
        ];
    }
}
