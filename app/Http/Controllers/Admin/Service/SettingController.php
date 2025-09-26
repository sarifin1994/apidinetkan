<?php

namespace App\Http\Controllers\Admin\Service;

use App\Models\PppoeSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $isolir = PppoeSetting::where('group_id', $request->user()->id_group)->get();
            return response()->json([
                'success' => true,
                'data' => $isolir,
            ]);
        }
        return view('pppoe.settings.index');
    }

    public function update(Request $request, PppoeSetting $setting)
    {
        if ($request->isolir === 1) {
            $setting->update([
                'isolir' => $request->isolir,
            ]);
        } else {
            $setting->update([
                'isolir' => $request->isolir,
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $setting,
        ]);
    }
}
