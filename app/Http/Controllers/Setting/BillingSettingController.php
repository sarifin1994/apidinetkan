<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\BillingSetting;

class BillingSettingController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $billing = BillingSetting::where('shortname',multi_auth()->shortname)->first();
            return response()->json([
                'success' => true,
                'data' => $billing,
            ]);
        }
        return view('backend.setting.billing.index_new');
    }

    public function update(Request $request, BillingSetting $billing)
    {
        $billing->update([
            'due_bc' => $request->due_bc,
            'inv_fd' => $request->inv_fd,
            'suspend_date' => $request->suspend_date,
            'suspend_time' => $request->suspend_time,
            'notif_ir' => $request->notif_ir,
            'notif_it' => $request->notif_it,
            'notif_ps' => $request->notif_ps,
            'notif_sm' => $request->notif_sm,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $billing,
        ]);
    }

}
