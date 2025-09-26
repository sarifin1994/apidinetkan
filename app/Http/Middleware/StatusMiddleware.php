<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class StatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$statuses): Response
    {
        // Misalnya, $roles adalah array role yang diizinkan
        $allowed = false;

        // Cek jika guard "user" telah terautentikasi
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if (in_array($user->status, $statuses)) {
                $allowed = true;
            }
        }

        // Cek jika guard "mitra" telah terautentikasi
        if (Auth::guard('mitra')->check()) {
            $mitra = Auth::guard('mitra')->user();
            if (in_array($mitra->status, $statuses)) {
                $allowed = true;
            }
        }
        if (Auth::guard('admin_dinetkan')->check()) {
            $admin = Auth::guard('admin_dinetkan')->user();
            if (in_array($admin->status, $statuses)) {
                $allowed = true;
            }
        }

        if ($allowed) {
            return $next($request);
        }
        // abort(401);

        // Cek jika status = 2
        if (
            Auth::guard('web')->check() &&
            Auth::guard('web')->user()->status == 2
            // (Auth::guard('mitra')->check() && Auth::guard('mitra')->user()->status == 2)
        ) {
            return redirect('/verify');
        }
        if (
            Auth::guard('web')->check() &&
            Auth::guard('web')->user()->status == 4
        ) {
             return redirect('/admin/account/after/get_info_dinetkan');
//            return redirect('/');
        }

        return redirect('/account');
    }
}
