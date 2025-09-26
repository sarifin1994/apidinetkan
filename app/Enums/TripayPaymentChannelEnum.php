<?php

namespace App\Enums;

enum TripayPaymentChannelEnum: string
{
    case PERMATA_VIRTUAL_ACCOUNT = 'PERMATAVA';
    case BNI_VIRTUAL_ACCOUNT = 'BNIVA';
    case BRI_VIRTUAL_ACCOUNT = 'BRIVA';
    case MANDIRI_VIRTUAL_ACCOUNT = 'MANDIRIVA';
    case CIMB_VIRTUAL_ACCOUNT = 'CIMBVA';
    case BSI_VIRTUAL_ACCOUNT = 'BSIVA';
    case OCBC_VIRTUAL_ACCOUNT = 'OCBCVA';
    case DANAMON_VIRTUAL_ACCOUNT = 'DANAMONVA';
    case OTHER_BANK_VIRTUAL_ACCOUNT = 'OTHERBANKVA';
    case ALFAMART = 'ALFAMART';
    case INDOMARET = 'INDOMARET';
    case ALFAMIDI = 'ALFAMIDI';
    case QRIS_SHOPEE_PAY = 'QRIS';
    case QRIS = 'QRIS2';
    case DANA = 'DANA';
    case SHOPEE_PAY = 'SHOPEEPAY';

    public function label(): string
    {
        return match ($this) {
            self::PERMATA_VIRTUAL_ACCOUNT => 'Permata Virtual Account',
            self::BNI_VIRTUAL_ACCOUNT => 'BNI Virtual Account',
            self::BRI_VIRTUAL_ACCOUNT => 'BRI Virtual Account',
            self::MANDIRI_VIRTUAL_ACCOUNT => 'Mandiri Virtual Account',
            self::CIMB_VIRTUAL_ACCOUNT => 'CIMB Virtual Account',
            self::BSI_VIRTUAL_ACCOUNT => 'BSI Virtual Account',
            self::OCBC_VIRTUAL_ACCOUNT => 'OCBC Virtual Account',
            self::DANAMON_VIRTUAL_ACCOUNT => 'Danamon Virtual Account',
            self::OTHER_BANK_VIRTUAL_ACCOUNT => 'Other Bank Virtual Account',
            self::ALFAMART => 'Alfamart',
            self::INDOMARET => 'Indomaret',
            self::ALFAMIDI => 'Alfamidi',
            self::QRIS_SHOPEE_PAY => 'QRIS ShopeePay',
            self::QRIS => 'QRIS',
            self::DANA => 'DANA',
            self::SHOPEE_PAY => 'ShopeePay',
        };
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::PERMATA_VIRTUAL_ACCOUNT->value => 'Permata Virtual Account',
            self::BNI_VIRTUAL_ACCOUNT->value => 'BNI Virtual Account',
            self::BRI_VIRTUAL_ACCOUNT->value => 'BRI Virtual Account',
            self::MANDIRI_VIRTUAL_ACCOUNT->value => 'Mandiri Virtual Account',
            self::CIMB_VIRTUAL_ACCOUNT->value => 'CIMB Virtual Account',
            self::BSI_VIRTUAL_ACCOUNT->value => 'BSI Virtual Account',
            self::OCBC_VIRTUAL_ACCOUNT->value => 'OCBC Virtual Account',
            self::DANAMON_VIRTUAL_ACCOUNT->value => 'Danamon Virtual Account',
            self::OTHER_BANK_VIRTUAL_ACCOUNT->value => 'Other Bank Virtual Account',
            self::ALFAMART->value => 'Alfamart',
            self::INDOMARET->value => 'Indomaret',
            self::ALFAMIDI->value => 'Alfamidi',
            self::QRIS_SHOPEE_PAY->value => 'QRIS ShopeePay',
            self::QRIS->value => 'QRIS',
            self::DANA->value => 'DANA',
            self::SHOPEE_PAY->value => 'ShopeePay',
        ];
    }
}
