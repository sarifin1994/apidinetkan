<?php

namespace App\Enums;

use App\Models\License;

enum OltDeviceEnum: string
{
    case HSGQ = 'hsgq_e04m';
    case VSOL = 'vsol';
    case HIOSO_HA7304 = 'hioso_ha7304';
    case HIOSO_HA7302 = 'hioso_ha7302';
    case ZTE = 'zte';
    case FIBERHOME = 'fiberhome';

    public function label(): string
    {
        return match ($this) {
            self::HSGQ => 'HSGQ - 4 Port',
            self::VSOL => 'V-SOL - 4 Port',
            self::HIOSO_HA7304 => 'HIOSO - 4 Port',
            self::HIOSO_HA7302 => 'HIOSO - 2 Port',
            self::ZTE => 'ZTE',
            self::FIBERHOME => 'FIBERHOME',
        };
    }

    public static function fromValue(string $value): self
    {
        return match ($value) {
            self::HSGQ->value => self::HSGQ,
            self::VSOL->value => self::VSOL,
            self::HIOSO_HA7304->value => self::HIOSO_HA7304,
            self::HIOSO_HA7302->value => self::HIOSO_HA7302,
            self::ZTE->value => self::ZTE,
            self::FIBERHOME->value => self::FIBERHOME,
        };
    }

    public static function getSelectOptions(): array
    {
        return [
            self::HSGQ->value => self::HSGQ->label(),
            self::VSOL->value => self::VSOL->label(),
            self::HIOSO_HA7304->value => self::HIOSO_HA7304->label(),
            self::HIOSO_HA7302->value => self::HIOSO_HA7302->label(),
            self::ZTE->value => self::ZTE->label(),
            self::FIBERHOME->value => self::FIBERHOME->label(),
        ];
    }

    public static function getValues(): array
    {
        return [
            self::HSGQ->value,
            self::VSOL->value,
            self::HIOSO_HA7304->value,
            self::HIOSO_HA7302->value,
            self::ZTE->value,
            self::FIBERHOME->value,
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
            } elseif ($model === self::ZTE->value) {
                $supportedModels[] = self::ZTE;
            
            } elseif ($model === self::FIBERHOME->value) {
                $supportedModels[] = self::FIBERHOME;
            }
        }

        return $supportedModels;
    }
}
