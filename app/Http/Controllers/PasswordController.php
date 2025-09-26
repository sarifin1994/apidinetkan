<?php


namespace App\Http\Controllers;


use App\Mail\TestEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function index()
    {
        return view('backend.password.index_new');
    }

    public function change(Request $request){

        // Validasi input
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8'],
            'confirm_password' => ['required', 'min:8'],
        ]);

        $user = User::where('username', multi_auth()->username)->first();

        // Periksa apakah password lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password Lama Salah'
            ]);
        }
        if ($request->password != $request->confirm_password) {
            return response()->json([
                'success' => false,
                'message' => 'Password dan konfirmasi password berbeda'
            ]);
        }

        // Simpan password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password Berhasil Disimpan'
        ]);
    }
}
