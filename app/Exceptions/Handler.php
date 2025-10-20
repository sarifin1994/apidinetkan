<?php


namespace App\Exceptions;


use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;


class Handler
{
    protected function unauthenticated(Request $request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }
}
