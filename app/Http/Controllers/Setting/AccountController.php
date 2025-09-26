<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::where('username', multi_auth()->username)->with('license')->first();
        return view('backend.account.index_new', compact('user'));
    }

    // public function update(Request $request, Isolir $isolir)
    // {
    //     if ($request->isolir === 1) {
    //         $isolir->update([
    //             'isolir' => $request->isolir,
    //         ]);
    //     } else {
    //         $isolir->update([
    //             'isolir' => $request->isolir,
    //         ]);
    //     }
    //     //return response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data Berhasil Disimpan',
    //         'data' => $isolir,
    //     ]);
    // }
}
