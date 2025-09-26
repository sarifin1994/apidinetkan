<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DinetkanAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Partnership\Mitra;
use Session;

class LoginController extends Controller
{
    // public function login()
    // {
    //     if (Auth::guard('web')->check() || Auth::guard('mitra')->check()) {
    //         return redirect('/dashboard');
    //     } else {
    //         return view('backend/auth/login');
    //     }
    // }

    // public function auth(Request $request)
    // {
    //     $data = [
    //         'username' => $request->input('username'),
    //         'password' => $request->input('password'),
    //     ];

    //     // Coba login menggunakan mekanisme default (misalnya tabel users)
    //     if (Auth::attempt($data)) {
    //         return redirect('/');
    //     }
    //     // Ambil user dari table lain
    //     $mitra = Mitra::where('id_mitra', $data['username'])->first();

    //     // Cek apakah user ditemukan dan password sesuai
    //     if ($mitra && Hash::check($data['password'], $mitra->password)) {
    //         Auth::guard('mitra')->login($mitra);
    //         return redirect('/');
    //     }

    //     Session::flash('error', 'Username atau password salah');
    //     return redirect('/');
    // }

    // public function logout()
    // {
    //     if (Auth::guard('web')->check()) {
    //         Auth::guard('web')->logout();
    //     }
    //     if (Auth::guard('mitra')->check()) {
    //         Auth::guard('mitra')->logout();
    //     }
    //     return redirect('/');
    // }

    public function login(Request $request)
    {
        // Jika user sudah login di salah satu guard, redirect ke dashboard
        if (Auth::guard('web')->check() || Auth::guard('mitra')->check() || Auth::guard('admin_dinetkan')->check()) {
            return redirect()->route('dashboard');
        }
        return view('backend.auth.login_new');
    }

    // public function auth(Request $request)
    // {
    //     // Validasi input untuk keamanan dan konsistensi data
    //     $credentials = $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Coba login menggunakan mekanisme default (misalnya tabel users)
    //     if (Auth::attempt($credentials)) {
    //         // Regenerasi session untuk mencegah session fixation
    //         $request->session()->regenerate();
    //         return redirect()->intended('/');
    //     }

    //     // Coba login sebagai mitra (dari tabel lain)
    //     $mitra = Mitra::where('id_mitra', $credentials['username'])->first();
    //     if ($mitra && Hash::check($credentials['password'], $mitra->password)) {
    //         Auth::guard('mitra')->login($mitra);
    //         $request->session()->regenerate();
    //         return redirect()->intended('/');
    //     }

    //     // Jika autentikasi gagal, kembalikan ke halaman login dengan pesan error
    //     Session::flash('error', 'Username atau password salah');
    //     return redirect('/');
    // }

    // public function logout(Request $request)
    // {
    //     // Logout dari kedua guard tanpa perlu cek terlebih dahulu,
    //     // karena logout pada guard yang tidak aktif tidak menimbulkan error.
    //     Auth::guard('web')->logout();
    //     Auth::guard('mitra')->logout();

    //     // Invalidate session dan regenerasi token untuk keamanan
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/');
    // }

    public function auth(Request $request)
    {
        // Validasi input untuk keamanan dan konsistensi data
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login menggunakan mekanisme default (misalnya tabel users)
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // Coba login sebagai mitra (dari tabel lain)
        $mitra = Mitra::where('id_mitra', $credentials['username'])->first();
        if ($mitra && Hash::check($credentials['password'], $mitra->password)) {
            if($mitra->login == 0){
                return back()->withErrors([
                    'error' => 'Akun anda tidak diizinkan login',
                ]);
            }
            Auth::guard('mitra')->login($mitra);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // Coba login sebagai admin dinetkan (dari tabel lain)
        $admin = DinetkanAdmin::where('username', $credentials['username'])->first();
        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            Auth::guard('admin_dinetkan')->login($admin);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

    
        // Jika autentikasi gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'error' => 'Username atau password salah',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout dari kedua guard tanpa perlu cek terlebih dahulu,
        // karena logout pada guard yang tidak aktif tidak menimbulkan error.
        Auth::guard('web')->logout();
        Auth::guard('mitra')->logout();

        // Invalidate session dan regenerasi token untuk keamanan
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // Redirect ke halaman login untuk memastikan token CSRF baru dimuat
        return redirect('/');
    }
}
