<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Isolir;

class IsolirController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $isolir = Isolir::where('shortname',multi_auth()->shortname)->first();
            return response()->json([
                'success' => true,
                'data' => $isolir,
            ]);
        }
        return view('backend.setting.isolir.index_new');
    }

    public function update(Request $request, Isolir $isolir)
    {
        if ($request->isolir === 1) {
            $isolir->update([
                'isolir' => $request->isolir,
            ]);
        } else {
            $isolir->update([
                'isolir' => $request->isolir,
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $isolir,
        ]);
    }
}
