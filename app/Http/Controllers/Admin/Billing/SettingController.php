<?php

namespace App\Http\Controllers\Admin\Billing;

use Illuminate\Http\Request;
use App\Models\BillingSetting;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $billing = BillingSetting::where('group_id', $request->user()->id_group)->get();
            return response()->json([
                'success' => true,
                'data' => $billing,
            ]);
        }

        return view('billing.settings.index');
    }

    public function update(Request $request, BillingSetting $setting)
    {
        $setting->update([
            'due_bc' => $request->due_bc,
            'inv_fd' => $request->inv_fd,
            'suspend_date' => $request->suspend_date,
            'suspend_time' => $request->suspend_time,
            'notif_bi' => $request->notif_bi,
            'notif_it' => $request->notif_it,
            'notif_ps' => $request->notif_ps,
            'notif_sm' => $request->notif_sm,
            'payment_gateway' => $request->payment_gateway,
            'bank_account' => $request->manual
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $setting,
        ]);
    }
}
