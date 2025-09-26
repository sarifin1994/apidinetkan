<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use App\Models\Setting\Company;
use Barryvdh\DomPDF\Facade\Pdf;


class PrintController extends Controller
{
    // public function print(){
    //     $no_invoice = last(request()->segments());
    //     $invoice = Invoice::where('shortname', multi_auth()->shortname)
    //         ->where('no_invoice', $no_invoice)
    //         ->with('rpppoe')
    //         ->first();
    //     $company = Company::where('shortname',multi_auth()->shortname)->first();
    //     // $pdf = Pdf::loadView('backend.billing.invoice.print_thermal', compact('invoice'));
    //     // return $pdf->stream();
    //     return view('backend.invoice.print.a4',compact('invoice','company'));

    // }

    public function printUnpaid(Request $request){
        $invoices = Invoice::where('shortname', multi_auth()->shortname)
            ->whereIn('id', $request->ids)
            ->with('rpppoe')
            ->get();
        $company = Company::where('shortname',multi_auth()->shortname)->first();
        if($request->template == 'a4'){
            $html = view('backend.invoice.print.a4s',compact('invoices','company'))->render();
        }else if($request->template == 'a5'){
            $html = view('backend.invoice.print.a5s',compact('invoices','company'))->render();
        }else if($request->template == 'thermal'){
            $html = view('backend.invoice.print.thermal',compact('invoices','company'))->render();
        }
        return response()->json([
            'success' => true,
            'message' => 'Silakan Cetak Invoice Anda',
            'data' => $html,
        ]);
    }
}
