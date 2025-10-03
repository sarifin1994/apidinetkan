<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Partnership\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // Register user baru
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    // Login pakai Auth::attempt()
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Username atau password salah'], 401);
    //     }

    //     $user = Auth::user();

    //     // Buat token Sanctum
    //     $token = $user->createToken('mobile-token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login berhasil',
    //         'user' => $user,
    //         'token' => $token,
    //         'token_type' => 'Bearer'
    //     ]);
    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 🔹 Coba login sebagai User biasa
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('mobile-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil (User)',
                'user'    => $user,
                'tipe'    => $user->is_dinetkan == 1 ? 'kemitraan' : 'pppoe',
                'token'   => $token,
                'token_type' => 'Bearer'
            ]);
        }

        // 🔹 Coba login sebagai Mitra
        $mitra = Mitra::where('id_mitra', $credentials['username'])->first();
        if ($mitra && Hash::check($credentials['password'], $mitra->password)) {
            if ($mitra->login == 0) {
                return response()->json([
                    'message' => 'Akun anda tidak diizinkan login'
                ], 403);
            }

            Auth::guard('mitra')->login($mitra);

            // Kalau juga mau kasih Sanctum token ke mitra
            $token = $mitra->createToken('mitra-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil (Mitra)',
                'user'   => $mitra,
                'tipe'   => 'sales',
                'token'   => $token,
                'token_type' => 'Bearer'
            ]);
        }

        // Kalau dua-duanya gagal
        return response()->json([
            'message' => 'Username atau password salah'
        ], 401);
    }


    // Logout -> hapus token aktif
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // Ambil data user login
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Hapus semua token user (force logout dari semua device)
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout dari semua device berhasil'
        ]);
    }
}
