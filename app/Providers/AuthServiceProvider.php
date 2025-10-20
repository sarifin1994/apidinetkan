<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Partnership\Mitra;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Pastikan Sanctum pakai model token bawaan
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // 🔹 Tambahkan resolver supaya Sanctum mengenali dua model
        Sanctum::authenticateAccessTokensUsing(function ($accessToken, $isValid) {
            if (! $isValid) {
                return false;
            }

            $model = $accessToken->tokenable_type;

            return in_array($model, [
                User::class,
                Mitra::class,
            ]);
        });
    }
}
