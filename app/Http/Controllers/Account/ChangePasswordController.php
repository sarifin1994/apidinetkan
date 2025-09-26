<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    public function update(ChangePasswordRequest $request)
    {
        $user = $request->user();
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password Berhasil Diubah',
        ]);
    }
}
