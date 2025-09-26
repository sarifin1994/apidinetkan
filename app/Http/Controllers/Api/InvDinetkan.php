<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\AdminDinetkanInvoice;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\Pppoe\PppoeUser;
use App\Models\Setting\Mduitku;
use App\Models\UserDinetkan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InvDinetkan extends Controller
{
    public function unpaid(Request $request)
    {
        $invoices = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
            ->get();
        return response()->json($invoices);
    }

    public function paid(Request $request)
    {
        $invoices = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::PAID)
            ->get();
        return response()->json($invoices);
    }

    public function expired(Request $request)
    {
        $invoices = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL])
            ->where('due_date','<', Carbon::now())
            ->get();

        return response()->json($invoices);
    }

    public function get_by_invoice_id(Request $request){
        $request->invoice_type = 'mitra';

        if($request->invoice_type == 'pppoe'){
            $inv = Invoice::where('id', $request->invoice_id)
                ->first();
            if($inv){
                $user = PppoeUser::where('id', $inv->id_pelanggan)->first();
                $company_name = "";
                $data = [
                    'invoice_id' => $inv->id,
                    'no_invoice' => $inv->no_invoice,
                    'due_date' => $inv->due_date,
                    'item' => $inv->item,
                    'price' => $inv->price,
                    'ppn' => $inv->ppn,
                    'total_ppn' => $inv->total_ppn,
                    'price_adon' => $inv->price_adon,
                    'price_adon_monthly' => $inv->price_adon_monthly,
                    'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                    'status' => $inv->status,
                    'status_desc' => Str::upper($inv->status),
                    'payment_url' => route('bayar.invoice',$inv->no_invoice),
                    'service_id' => $user->id_pelanggan
                ];
                $response = [
                    'fullname' => $user->fullname,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'id_pelanggan' => $user->id_pelanggan,
                    //            'bulan' => $request->bulan,
                    //            'tahun' => $request->tahun,
                    'perusahaan' => $company_name,
                    'data' => $data
                ];
                return response()->json($response, 200);
            } else{
                return response()->json(['message' => 'Invoice not found'], 500);
            }

        }
        if($request->invoice_type == 'mitra'){
            $inv = AdminDinetkanInvoice::where('id', $request->invoice_id)
                ->with('admin')
                ->first();
            if($inv){
                $mapping = MappingUserLicense::query()->where('id',$inv->id_mapping)->first();
                $user = UserDinetkan::where('dinetkan_user_id', $inv->dinetkan_user_id)->first();
                $company_name = "";
                $company = Company::where('group_id', $user->id)->first();
                if($company){
                    $company_name = $company->name;
                }
                $status_desc = "UNPAID";
                if($inv->status == DinetkanInvoiceStatusEnum::PAID->value){
                    $status_desc = "PAID";
                }
                $mappingadon = MappingAdons::query()->where('id_mapping', $inv->id_mapping)->get();
                $data = [
                    'invoice_id' => $inv->id,
                    'no_invoice' => $inv->no_invoice,
                    'due_date' => $inv->due_date,
                    'item' => $inv->item,
                    'price' => $inv->price,
                    'ppn' => $inv->ppn,
                    'total_ppn' => $inv->total_ppn,
                    'price_adon' => $inv->price_adon,
                    'price_adon_monthly' => $inv->price_adon_monthly,
                    'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                    'status' => $inv->status,
                    'status_desc' => $status_desc,
                    'payment_url' => "",
                    'service_id' => $mapping ? $mapping->service_id : 0,
                    'mappingadon' => $mappingadon
                ];
                $response = [
                    'fullname' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'id_pelanggan' => $user->dinetkan_user_id,
                    //            'bulan' => $request->bulan,
                    //            'tahun' => $request->tahun,
                    'perusahaan' => $company_name,
                    'data' => $data
                ];
                return response()->json($response, 200);
            } else{
                return response()->json(['message' => 'Invoice not found'], 500);
            }
        }
    }


    protected function get_payment_method()
    {
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        // Set kode merchant anda
        $merchantCode = $duitku->id_merchant;
        // Set merchant key anda
        $apiKey = $duitku->api_key;
        // catatan: environtment untuk sandbox dan passport berbeda

        $datetime = date('Y-m-d H:i:s');
        $paymentAmount = 10000;
        $signature = hash('sha256', $merchantCode . $paymentAmount . $datetime . $apiKey);

        $params = array(
            'merchantcode' => $merchantCode,
            'amount' => $paymentAmount,
            'datetime' => $datetime,
            'signature' => $signature
        );
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        if (env('APP_ENV') == 'production') {
            $url = 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        }
        $response = makeRequest($url, "POST", $params);
        $data = $response;
        $paymentMethod = [];
        if (isset($data['paymentFee'])) {
            $filteredVA = collect($data['paymentFee'])->filter(function ($item) {
                return Str::contains($item['paymentName'], ' VA');
            });

            // Contoh: ambil hanya paymentName-nya
            $paymentNames = $filteredVA->pluck('paymentName', 'paymentMethod');

            // Tampilkan
            foreach ($paymentNames as $key => $val) {
                $paymentMethod[] = [
                    'payment_method' => $key,
                    'bank_name' => $val,
                    'panduan' => get_panduan($key)
                ];
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }
}
