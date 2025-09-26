<?php

namespace App\Http\Controllers\Admin\Integration;

use App\Models\Xendit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class XenditController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;

        if (!$license || !$license->payment_gateway) {
            return redirect()->route('admin.account.info')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan untuk mengupgrade lisensi anda.');
        }

        $xendit = Xendit::where('group_id', $request->user()->id_group)->first();

        if (!$xendit) {
            $xendit = Xendit::create([
                'group_id' => $request->user()->id_group,
                'public_key' => '',
                'secret_key' => '',
                'webhook_verification_key' => '',
                'admin_fee' => 0,
                'status' => false,
            ]);
        }

        return view('integrations.xendit.index', compact('xendit'));
    }

    public function update(Request $request, Xendit $xendit)
    {
        $xendit->update([
            'public_key' => $request->public_key,
            'secret_key' => $request->secret_key,
            'webhook_verification_key' => $request->webhook_verification_key,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $xendit,
        ]);
    }
}
