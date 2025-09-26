<?php

namespace App\Http\Controllers\Admin\Integration;

use App\Models\Midtrans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MidtransController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;

        if (!$license || !$license->payment_gateway) {
            return redirect()->route('admin.account.info')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan untuk mengupgrade lisensi anda.');
        }

        $midtrans = Midtrans::where('group_id', $request->user()->id_group)->first();
        return view('integrations.midtrans.index', compact('midtrans'));
    }

    public function update(Request $request, Midtrans $midtran)
    {
        $midtran->update([
            'id_merchant' => $request->id_merchant,
            'client_key' => $request->client_key,
            'server_key' => $request->server_key,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $midtran,
        ]);
    }
}
