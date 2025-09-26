<?php

namespace App\Http\Controllers\Admin\Integration;

use App\Models\Tripay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TripayController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;

        if (!$license || !$license->payment_gateway) {
            return redirect()->route('admin.account.info')->with('error', 'Lisensi anda tidak mendukung fitur ini, silakan untuk mengupgrade lisensi anda.');
        }

        $tripay = Tripay::where('group_id', $request->user()->id_group)->first();

        if (!$tripay) {
            Tripay::create([
                'group_id' => $request->user()->id_group,
                'merchant_code' => '',
                'api_key'        => '',
                'private_key'    => '',
                'admin_fee'      => 0,
                'status'         => 0,
            ]);
        }

        return view('integrations.tripay.index', compact('tripay'));
    }

    public function update(Request $request, Tripay $tripay)
    {
        $tripay->update([
            'merchant_code' => $request->merchant_code,
            'api_key' => $request->api_key,
            'private_key' => $request->private_key,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Successfully Saved',
            'data' => $tripay,
        ]);
    }
}
