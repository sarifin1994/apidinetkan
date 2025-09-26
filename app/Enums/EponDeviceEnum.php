<?php

namespace App\Enums;

use App\Models\License;

enum EponDeviceEnum: string
{
    case HSGQ = 'hsgq_e04m';
    case VSOL = 'vsol';
    case HIOSO_HA7304 = 'hioso_ha7304';
    case HIOSO_HA7302 = 'hioso_ha7302';

    public function label(): string
    {
        return match ($this) {
            self::HSGQ => 'HSGQ - 4 Port',
            self::VSOL => 'V-SOL - 4 Port',
            self::HIOSO_HA7304 => 'HIOSO - 4 Port',
            self::HIOSO_HA7302 => 'HIOSO - 2 Port',
        };
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            self::HSGQ->value => self::HSGQ,
            self::VSOL->value => self::VSOL,
            self::HIOSO_HA7304->value => self::HIOSO_HA7304,
            self::HIOSO_HA7302->value => self::HIOSO_HA7302,
        };
    }

    public static function getSelectOptions(): array
    {
        return [
            self::HSGQ->value => self::HSGQ->label(),
            self::VSOL->value => self::VSOL->label(),
            self::HIOSO_HA7304->value => self::HIOSO_HA7304->label(),
            self::HIOSO_HA7302->value => self::HIOSO_HA7302->label(),
        ];
    }

    public static function getValues(): array
    {
        return [
            self::HSGQ->value,
            self::VSOL->value,
            self::HIOSO_HA7304->value,
            self::HIOSO_HA7302->value,
        ];
    }

    public static function getSupportedModels(License $license): array
    {
        $models = $license->olt_models;

        $supportedModels = [];

        foreach ($models as $model) {
            if ($model === self::HSGQ->value) {
                $supportedModels[] = self::HSGQ;
            } elseif ($model === self::VSOL->value) {
                $supportedModels[] = self::VSOL;
            } elseif ($model === self::HIOSO_HA7304->value) {
                $supportedModels[] = self::HIOSO_HA7304;
            } elseif ($model === self::HIOSO_HA7302->value) {
                $supportedModels[] = self::HIOSO_HA7302;
            }
        }

        return $supportedModels;
    }
}
