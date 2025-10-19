<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Http\Controllers\Controller;
use App\Models\AdminDinetkanInvoice;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\Pppoe\PppoeUser;
use App\Models\Setting\Mduitku;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Settings\SiteDinetkanSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InvDinetkanController extends Controller
{
    public function unpaid(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::UNPAID);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('no_invoice')) {
            $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
        }

        if ($request->filled('item')) {
            $query->where('item', 'like', '%' . $request->item . '%');
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('paid_date', [
                $request->from_date,
                $request->to_date
            ]);
        }
        $invoices = $query->orderBy('paid_date', 'desc')->paginate($perPage);
        return response()->json($invoices);
    }

    public function paid(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', DinetkanInvoiceStatusEnum::PAID);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('no_invoice')) {
            $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
        }

        if ($request->filled('item')) {
            $query->where('item', 'like', '%' . $request->item . '%');
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('paid_date', [
                $request->from_date,
                $request->to_date
            ]);
        }
        $invoices = $query->orderBy('paid_date', 'desc')->paginate($perPage);
        return response()->json($invoices);
    }

    public function expired(Request $request)
    {

        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = AdminDinetkanInvoice::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->whereIn('status', [DinetkanInvoiceStatusEnum::EXPIRED,DinetkanInvoiceStatusEnum::CANCEL]);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('no_invoice')) {
            $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
        }

        if ($request->filled('item')) {
            $query->where('item', 'like', '%' . $request->item . '%');
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('paid_date', [
                $request->from_date,
                $request->to_date
            ]);
        }
        $invoices = $query->orderBy('paid_date', 'desc')->paginate($perPage);
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

    public function generate_va(Request $request){
        $invoice = AdminDinetkanInvoice::where('id', $request->invoice_id)->first();
        // otc
        $amount_ppn_otc = 0;
        $gross_amount_otc = 0;
        if($invoice->price_otc > 0){
            if($invoice->ppn_otc > 0){
                $amount_ppn_otc = $invoice->price_otc * $invoice->ppn_otc / 100;
            }
            $gross_amount_otc = $invoice->price_otc + $amount_ppn_otc;
        }
        // otc
        $amount_discount = $invoice->price * $invoice->discount / 100;
        $amount_ppn      = ($invoice->price - $invoice->discount_coupon - $amount_discount) * $invoice->ppn / 100;
        $discount_coupon = $invoice->discount_coupon;

        $gross_amount    = $invoice->price + $invoice->fee + $amount_ppn - $amount_discount - $invoice->discount_coupon;
        $gross_amount = $gross_amount  + $invoice->price_adon  + $invoice->price_adon_monthly;
        $itemDetails = [
            [
                'name'     => $invoice->item,
                'price'    => (int) $invoice->price,
                'quantity' => 1,
            ],
        ];

        if ($amount_ppn > 0) {
            $itemDetails[] = [
                'name'     => 'PPN',
                'price'    => (int) round($amount_ppn),
                'quantity' => 1,
            ];
        }

        if ($amount_discount > 0) {
            $itemDetails[] = [
                'name'     => 'Discount',
                'price'    => (int) -round($amount_discount),
                'quantity' => 1,
            ];
        }

        if ($discount_coupon > 0) {
            $itemDetails[] = [
                'name'     => 'Discount Promo',
                'price'    => (int) -round($discount_coupon),
                'quantity' => 1,
            ];
        }
        if ($invoice->fee > 0) {
            $itemDetails[] = [
                'name'     => 'Biaya Admin',
                'price'    => (int) $invoice->fee,
                'quantity' => 1,
            ];
        }

        $customerVaName = match (true) {
        $invoice instanceof Invoice => $invoice->member->full_name,
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->name,
            default => '',
        };
        $email = match (true) {
        $invoice instanceof Invoice => $invoice->member->email ?? '',
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->email ?? '',
            default => '',
        };
        $phoneNumber = match (true) {
        $invoice instanceof Invoice => $invoice->member->wa ?? '',
            $invoice instanceof AdminDinetkanInvoice => $invoice->admin->whatsapp ?? '',
            default => '',
        };

        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $user = User::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
        // true for sandbox mode
        $url = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

        if(env('APP_ENV') == 'production'){
            $url = "https://passport.duitku.com/webapi/api/merchant/v2/inquiry";
        }
        $timestamp = (int) round(microtime(true) * 1000);
        $merchantCode = $duitku->id_merchant;
        $paymentMethod = $request->payment_method;
        $signature = md5($merchantCode . $invoice->no_invoice . (int) round($gross_amount) . $duitku->api_key);
        $transaction = [
            'merchantcode'    => $merchantCode,
            'paymentMethod'   => $paymentMethod,
            'merchantOrderId' => $invoice->no_invoice,
            'paymentAmount'   => (int) round($gross_amount),
            'productDetails'  => $invoice->item,
            'additionalParam' => '',
            'merchantUserInfo' => '',
            'customerVaName'  => $customerVaName,
            'email'           => $email,
            'phoneNumber'     => $phoneNumber,
            'itemsDetails'    => $itemDetails,
            'customerDetails' => [
                'firstName'   => $customerVaName,
                'email'       => $email,
                'phoneNumber' => $phoneNumber,
            ],
            'callbackUrl'     => env('APP_URL')."notification/admin/duitku_dinetkan", //route('notification.admin.duitku_dinetkan'),
            'returnUrl'       => env('APP_URL')."admin/invoice_dinetkan/".$invoice->no_invoice,//route('admin.invoice_dinetkan', ),
            'expiryPeriod'    => 60 * 24, // 1440 minutes
            'signature'       => $signature
        ];

        $response = makeRequest($url, "POST", $transaction);
        if(isset($response['statusCode'])){
            if($response['statusCode'] == '00' && $response['vaNumber'] != ''){
                $data_update=[
                    'virtual_account' => $response['vaNumber'],
                    'bank' => $request->payment_method,
                    'bank_name' => $request->bank_name,
                    'reference' => $response['reference']
                ];
                $invoice->update($data_update);
                return [
                    'vaNumber' => $response['vaNumber'],
                    'bank_name' => $request->bank_name,
                    'panduan' => get_panduan($request->payment_method)
                ];
            }
            return $response;
        }
        return $response;
    }
}
