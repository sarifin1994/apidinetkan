<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKeyExt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil dari query string, body, atau header
        $apiKey = $request->api_key_ext ?? $request->header('Api-Key-Ext');

        // Validasi API Key
        if (!$apiKey || $apiKey !== env('API_KEY_EXT')) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        return $next($request);
    }
}
