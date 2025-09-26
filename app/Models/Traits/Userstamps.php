<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait Userstamps
{
    public static function bootUserstamps()
    {
        static::creating(function ($model) {
            $username = self::getUsernameFromMultiAuth();
            if ($username) {
                $model->setAttribute('create_at_by', $username);
                $model->setAttribute('update_at_by', $username);
            }
        });

        static::updating(function ($model) {
            $username = self::getUsernameFromMultiAuth();
            if ($username) {
                $model->setAttribute('update_at_by', $username);
            }
        });
    }

    protected static function getUsernameFromMultiAuth(): ?string
    {
        if (function_exists('multi_auth') && multi_auth()) {
            return multi_auth()->username ?? null;
        }

        return null;
    }
}
