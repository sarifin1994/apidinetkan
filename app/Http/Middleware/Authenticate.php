<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }

    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            abort(response()->json(['message' => 'Unauthenticated.'], 401));
        }
    }
}
