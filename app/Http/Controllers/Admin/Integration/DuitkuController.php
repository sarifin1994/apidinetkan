<?php

namespace App\Http\Controllers\Admin\Integration;

use App\Models\Duitku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DuitkuController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;

        if (!$license || !$license->payment_gateway) {
            return redirect()->route('admin.account.info')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan untuk mengupgrade lisensi anda.');
        }

        $duitku = Duitku::where('group_id', $request->user()->id_group)->first();

        if (!$duitku) {
            $duitku = Duitku::create([
                'group_id' => $request->user()->id_group,
                'id_merchant' => '',
                'api_key' => '',
                'admin_fee' => 0,
                'status' => false,
            ]);
        }

        return view('integrations.duitku.index', compact('duitku'));
    }

    public function update(Request $request, Duitku $duitku)
    {
        $duitku->update([
            'id_merchant' => $request->id_merchant,
            'api_key' => $request->api_key,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $duitku,
        ]);
    }
}
