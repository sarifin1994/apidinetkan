<?php

namespace App\Http\Controllers\Setting;
use App\Http\Controllers\Controller;
use App\Models\DuitkuLog;
use App\Models\Owner\License;
use App\Models\Setting\MduitkuOwner;
use App\Models\Setting\MidtransOwner;
use App\Models\Setting\WaServer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Setting\MidtransController;
use App\Http\Controllers\Setting\DuitkuController;
use App\Models\Setting\Midtrans;
use App\Models\Setting\Mduitku;
use App\Models\Invoice\Invoice;
use Yajra\DataTables\Facades\DataTables;

class PaymentOwnerController extends Controller
{
    public function index()
    {
        $midtrans = MidtransOwner::where('shortname', multi_auth()->shortname)->first();
        $duitku = MduitkuOwner::where('shortname', multi_auth()->shortname)->first();
        return view('backend.dinetkan.payment.index', compact('midtrans','duitku'));
    }
    public function bayar($id)
    {
        $invoice = Invoice::where('no_invoice', $id)->select('shortname')->first();

        if (!$invoice) {
            return view('backend.invoice.404');
        }

        $shortname = $invoice->shortname;

        $midtrans = MidtransOwner::where('shortname', $shortname)->select('status')->first();
        $duitku = MduitkuOwner::where('shortname', $shortname)->select('status')->first();
        if ($midtrans->status === 1) {
            return app(MidtransController::class)->bayar($id);
        } elseif (optional($duitku)->status === 1) {
            return app(DuitkuController::class)->bayar($id);
        } else {
            return view('backend.invoice.pg-404');
        }
    }


    public function show(MduitkuOwner $wa)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $wa,
        ]);
    }

    public function duitku_log(Request $request){

        if (request()->ajax()) {
            $logs = DuitkuLog::query()->where('shortname', 'owner_radiusqu')
                ->orderBy('created_at', 'desc')
                ->get();
            return DataTables::of($logs)
                ->addIndexColumn()
                ->toJson();
        }
        return view('backend.dinetkan.payment.duitku_log');
    }
}
