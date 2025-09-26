<?php

namespace App\Http\Controllers\Setting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Setting\DuitkuController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Setting\MidtransController;
use App\Models\Setting\Midtrans;
use App\Models\Setting\Mduitku;
use App\Models\Invoice\Invoice;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $midtrans = Midtrans::where('shortname', multi_auth()->shortname)->first();
        $duitku = Mduitku::where('shortname', multi_auth()->shortname)->first();
        if(!$midtrans){
            Midtrans::create([
                'shortname' => multi_auth()->shortname,
                'client_key' => "", // env('CLIENT_MIDTRANS'),
                'server_key' => "", // env('SERVER_MIDTRANS'),
                'id_merchant' => "", // env('MERCHANT_MIDTRANS'),
                'status' => 1,
            ]);
        }
        if (multi_auth()->license_id == 2) {
            return view('backend.account.limit');
        } else {
            return view('backend.setting.payment.index_new', compact('midtrans','duitku'));
        }
    }
    public function bayar($id)
    {
        $invoice = Invoice::where('no_invoice', $id)->select('shortname')->first();

        if (!$invoice) {
            return view('backend.invoice.404');
        }

        $shortname = $invoice->shortname;

        $midtrans = Midtrans::where('shortname', $shortname)->select('status')->first();
        $duitku = Mduitku::where('shortname', $shortname)->select('status')->first();
//        if ($midtrans->status === 1) {
////            return app(MidtransController::class)->bayar($id);
////        } elseif (optional($duitku)->status === 1) {
////            return app(DuitkuController::class)->bayar($id);
////        } else {
////            return view('backend.invoice.pg-404');
////        }
        return app(DuitkuController::class)->bayar($id);
    }
}
