<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Misalnya, $roles adalah array role yang diizinkan
        $allowed = false;

        // Cek jika guard "user" telah terautentikasi
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if (in_array($user->role, $roles)) {
                $allowed = true;
            }
        }

        // Cek jika guard "mitra" telah terautentikasi
        if (Auth::guard('mitra')->check()) {
            $mitra = Auth::guard('mitra')->user();
            if (in_array($mitra->role, $roles)) {
                $allowed = true;
            }
        }

        if (Auth::guard('mitra')->check()) {
            $mitra = Auth::guard('mitra')->user();
            if (in_array($mitra->ext_role, $roles)) {
                $allowed = true;
            }
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if (in_array($user->ext_role, $roles)) {
                $allowed = true;
            }
        }

        // Cek jika guard "mitra" telah terautentikasi
        if (Auth::guard('admin_dinetkan')->check()) {
            $admin = Auth::guard('admin_dinetkan')->user();
            if (in_array($admin->role, $roles)) {
                $allowed = true;
            }
        }

        if (Auth::guard('admin_dinetkan')->check()) {
            $admin = Auth::guard('admin_dinetkan')->user();
            if (in_array($admin->ext_role, $roles)) {
                $allowed = true;
            }
        }

        if ($allowed) {
            return $next($request);
        }
        // abort(401);

        return redirect('/account');
        // if ($request->user()->role !== $role) {
        //     abort(401);
        // }
        // return $next($request);
    }
}
