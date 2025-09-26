<?php


namespace App\Http\Controllers;


use App\Models\AdminDinetkanInvoice;
use App\Models\Company;
use App\Models\Hotspot\BillingHotspot;
use App\Models\Hotspot\HotspotProfile;
use App\Models\Hotspot\HotspotUser;
use App\Models\Invoice;
use App\Models\MappingUserLicense;
use App\Models\Pppoe\PppoeUser;
use App\Models\Setting\Mduitku;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Models\UserDinetkan;
use App\Models\UsersWhatsapp;
use App\Models\Whatsapp\Mpwa;
use App\Services\CustomMailerService;
use App\Settings\SiteDinetkanSettings;
use Illuminate\Http\Request;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Apibilling extends Controller
{
    public function __construct(
        private SiteDinetkanSettings $settings
    ) {

    }

    public function get_by_pelanggan(Request $request){
        if(!isset($request->id_pelanggan)){
            return response()->json(['message' => 'Parameter id_pelanggan required'], 500);
        }
        $response=[];
        // cari id pelanggan untuk user dinetkan
        $user = UserDinetkan::where('dinetkan_user_id', $request->id_pelanggan)->first();
        $data = [];
        if($user){
            $company_name = "";
            $company = Company::where('group_id', $user->id)->first();
            if($company){
                $company_name = $company->name;
            }
            $invoices = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
                ->where('dinetkan_user_id', $request->id_pelanggan)
                ->with('admin')
                ->get();
            if(count($invoices) > 0){
                foreach ($invoices as $inv){
                    $mapping = MappingUserLicense::query()->where('id',$inv->id_mapping)->first();
                    $data[] = [
                        'invoice_id' => $inv->id,
                        'invoice_type' => "mitra",
                        'no_invoice' => $inv->no_invoice,
                        'due_date' => $inv->due_date,
                        'item' => $inv->item,
                        'price' => $inv->price,
                        'ppn' => $inv->ppn,
                        'total_ppn' => $inv->total_ppn,
                        'price_adon' => $inv->price_adon,
                        'price_adon_monthly' => $inv->price_adon_monthly,
                        'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                        'service_id' => $mapping ? $mapping->service_id : 0

                    ];
                }
            }
            $response[] = [
                'fullname' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'id_pelanggan' => $request->id_pelanggan,
                'perusahaan' => $company_name,
                'data' => $data
            ];
        }

        // cari id pelanggan untuk service_id
        $mappings = MappingUserLicense::query()->where('service_id', $request->id_pelanggan)->get();
        if($mappings){
            foreach ($mappings as $mapp){
                $user = UserDinetkan::where('dinetkan_user_id', $mapp->dinetkan_user_id)->first();
                $data = [];
                if($user){
                    $company_name = "";
                    $company = Company::where('group_id', $user->id)->first();
                    if($company){
                        $company_name = $company->name;
                    }
                    $invoices = AdminDinetkanInvoice::where('status', DinetkanInvoiceStatusEnum::UNPAID)
                        ->where('id_mapping', $mapp->id)
                        ->with('admin')
                        ->get();
                    if(count($invoices) > 0){
                        foreach ($invoices as $inv){
                            $mapping = MappingUserLicense::query()->where('id',$inv->id_mapping)->first();
                            $data[] = [
                                'invoice_id' => $inv->id,
                                'invoice_type' => "mitra",
                                'no_invoice' => $inv->no_invoice,
                                'due_date' => $inv->due_date,
                                'item' => $inv->item,
                                'price' => $inv->price,
                                'ppn' => $inv->ppn,
                                'total_ppn' => $inv->total_ppn,
                                'price_adon' => $inv->price_adon,
                                'price_adon_monthly' => $inv->price_adon_monthly,
                                'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                                'service_id' => $mapping ? $mapping->service_id : 0

                            ];
                        }
                    }
                    $response[] = [
                        'fullname' => $user->name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'id_pelanggan' => $request->id_pelanggan,
                        'perusahaan' => $company_name,
                        'data' => $data
                    ];
                }
            }
        }


        // cari id pelanggan untuk user dinetkan
        $user = PppoeUser::where('id_pelanggan', $request->id_pelanggan)->first();
        $data = [];
        if($user){
            $company_name = "";
//            $company = Company::where('group_id', $user->id)->first();
//            if($company){
//                $company_name = $company->name;
//            }
            $invoices = Invoice::where('status', 'unpaid')
                ->where('id_pelanggan', $user->id)
                ->get();
            if(count($invoices) > 0){
                foreach ($invoices as $inv){
                    $data[] = [
                        'invoice_id' => $inv->id,
                        'invoice_type' => "pppoe",
                        'no_invoice' => $inv->no_invoice,
                        'due_date' => $inv->due_date,
                        'item' => $inv->item,
                        'price' => $inv->price,
                        'ppn' => $inv->ppn,
                        'total_ppn' => $inv->total_ppn,
                        'price_adon' => $inv->price_adon,
                        'price_adon_monthly' => $inv->price_adon_monthly,
                        'total' => ($inv->price + $inv->total_ppn + $inv->price_adon + $inv->price_adon_monthly),
                        'service_id' => $user->id_pelanggan

                    ];
                }
            }
            $response[] = [
                'fullname' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'id_pelanggan' => $request->id_pelanggan,
                'perusahaan' => $company_name,
                'data' => $data
            ];
        }
        return response()->json($response, 200);
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
        $paymentMethod[] = 'Select Payment';
        if (isset($data['paymentFee'])) {
            $filteredVA = collect($data['paymentFee'])->filter(function ($item) {
                return Str::contains($item['paymentName'], ' VA');
            });

            // Contoh: ambil hanya paymentName-nya
            $paymentNames = $filteredVA->pluck('paymentName', 'paymentMethod');

            // Tampilkan
            foreach ($paymentNames as $key => $val) {
                $paymentMethod[$key] = [
                    'bank' => $val,
                    'panduan' => get_panduan($key)
                ];
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }


    public function generate_va_api(Request $request, SiteDinetkanSettings $setting){
        if(isset($request->api_key_ext)){
            if(env('API_KEY_EXT') == $request->api_key_ext){
                $generate_va = $this->generate_va($request,$setting);
                return [
                    'status' => true,
                    'message' => 'Virtual Account Generated',
                    'data' => $generate_va,
                    'panduan' => get_panduan($request->payment_method)
                ];
            } else{
                return [
                    'status' => false,
                    'message' => 'Wrong api key',
                    'data' => []
                ];
            }
        }else{
            return [
                'status' => false,
                'message' => 'Wrong api key',
                'data' => []
            ];
        }
    }

    public function get_by_invoice_id(Request $request){
        if(!isset($request->invoice_id)){
            return response()->json(['message' => 'Parameter invoice_id required'], 500);
        }
        if(!isset($request->invoice_type)){
            return response()->json(['message' => 'Parameter invoice_type required'], 500);
        }
        if($request->invoice_type == 'pppoe'){
            $inv = Invoice::where('id', $request->invoice_id)
                ->first();
            if($inv){
                $user = PppoeUser::where('id', $inv->id_pelanggan)->first();
                $company_name = "";
//                $company = Company::where('group_id', $user->id)->first();
//                if($company){
//                    $company_name = $company->name;
//                }
//                $status_desc = "UNPAID";
//                if($inv->status == "paid"){
//                    $status_desc = "PAID";
//                }
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
                'service_id' => $mapping ? $mapping->service_id : 0
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


    public function generate_va(Request $request, SiteDinetkanSettings $setting){
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
        //        $gross_amount    = $invoice->price + $this->config->getAdminFee() + $amount_ppn - $amount_discount - $invoice->discount_coupon;

        $gross_amount    = $invoice->price + $invoice->fee + $amount_ppn - $amount_discount - $invoice->discount_coupon;
//        $gross_amount = $gross_amount + $gross_amount_otc;
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
            'callbackUrl'     => route('notification.admin.duitku_dinetkan'),
            'returnUrl'       => route('admin.invoice_dinetkan', $invoice->no_invoice),
            'expiryPeriod'    => 60 * 24, // 1440 minutes
            'signature'       => $signature
        ];

//        Log::info($transaction);
        $response = makeRequest($url, "POST", $transaction);
//        Log::info($response);
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

    public function get_payment_method_duitku()
    {
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        // Set kode merchant dan API key
        $merchantCode = $duitku->id_merchant;
        $apiKey = $duitku->api_key;

        // Waktu saat ini
        $datetime = now()->format('Y-m-d H:i:s');
        $paymentAmount = 10000;

        // Generate signature
        $signature = hash('sha256', $merchantCode . $paymentAmount . $datetime . $apiKey);

        // Siapkan data payload
        $payload = [
            'merchantcode' => $merchantCode,
            'amount' => $paymentAmount,
            'datetime' => $datetime,
            'signature' => $signature,
        ];
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        if(env('APP_ENV') == 'production'){
            $url = 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        }
        // Kirim request menggunakan Laravel HTTP Client (guzzle wrapper)
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->withBody(json_encode($payload), 'application/json')
            ->withoutVerifying() // skip SSL verify, gunakan hanya untuk sandbox
            ->post($url);

        // Cek response
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'Server Error',
                'status' => $response->status(),
                'message' => $response->json()['Message'] ?? 'Unknown error'
            ], $response->status());
        }
    }

    public function get_profile($id = 0){
        $profiles = HotspotProfile::query()->where('is_billing', 1)->get();
        if($id > 0){
            $profiles = HotspotProfile::query()->where('id', $id)->where('is_billing', 1)->get();
        }
        $list = [];
        foreach ($profiles as $profile){
            $userhs = HotspotUser::query()->where('profile', $profile->name)
                ->where('status_billing', 1)->get();
            $d = ((int) $profile->validity / (3600 * 24));
            $h = ((int) $profile->validity % (3600 * 24) / 3600);
            $m = ((int) $profile->validity % 3600 / 60);
            $voucher = new \stdClass();
            $voucher->id_profile = $profile->id;
            $voucher->name = $profile->name;
            $voucher->price = $profile->price;
            $voucher->validity = "";
            if ($d > 0) {
//                parts.push(d + ' HARI');
                $voucher->validity = $d . ' HARI';
            }

            if ($h > 0) {
//                parts.push(h + ' JAM');
                $voucher->validity = $h . ' JAM';
            }

            if ($m > 0) {
//                parts.push(m + ' BULAN');
                $voucher->validity = $m . ' BULAN';
            }
            $voucher->total_voucher = count($userhs);
            $list[] = $voucher;
        }
        return response()->json($list);
    }

    public function buy_voucher(Request $request){
        DB::beginTransaction();
        try{
            if($request->id_profile){
                $profile = HotspotProfile::query()->where('id', $request->id_profile)->first();
                if(!$profile){
                   return response()->json([
                       'data' => null,
                       'message' => 'data produk tidak di temukan !'
                   ], 500);
                }
                $trxNo = build_no_invoice('DN');

                $itemDetails = [
                    [
                        'name'     => "Pembelian Produk ".$profile->name,
                        'price'    => (int) $profile->price,
                        'quantity' => 1,
                    ],
                ];
                $customerVaName = "Billing Dinetkan";
                $email = $request->email;
                $phoneNumber = $request->whatsapp;

                $duitku = Mduitku::where('shortname', 'dinetkan')->first();
                // true for sandbox mode
                $url = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

                if(env('APP_ENV') == 'production'){
                    $url = "https://passport.duitku.com/webapi/api/merchant/v2/inquiry";
                }
                $timestamp = (int) round(microtime(true) * 1000);
                $gross_amount = $profile->price;
                $merchantCode = $duitku->id_merchant;
                $paymentMethod = $request->payment_method;
                $signature = md5($merchantCode . $trxNo . (int) round($gross_amount) . $duitku->api_key);
                $transaction = [
                    'merchantcode'    => $merchantCode,
                    'paymentMethod'   => $paymentMethod,
                    'merchantOrderId' => $trxNo,
                    'paymentAmount'   => (int) round($gross_amount),
                    'productDetails'  => "Pembelian Produk ".$profile->name,
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
                    'callbackUrl'     => route('notification.apibilling.callback'),
                    'returnUrl'       => 'coming soon',// route('admin.invoice_dinetkan', $invoice->no_invoice),
                    'expiryPeriod'    => 60 * 24, // 1440 minutes
                    'signature'       => $signature
                ];

                $response = makeRequest($url, "POST", $transaction);
                if(isset($response['statusCode'])){
                    if($response['statusCode'] == '00' && $response['vaNumber'] != ''){
                        $userhs = HotspotUser::query()->where('profile', $profile->name)
                            ->where('status_billing', 1)->first();
                        $userhs->update([
                            'status_billing' => 2
                        ]);
                        $dataInsert = [
                            'id_hotspot_profile' => $profile->id,
                            'name_hotspot_profile' => $profile->name,
                            'price' => $profile->price,
                            'virtual_account',
                            'bank' => $request->payment_method,
                            'bank_name' => $request->bank_name,
                            'status' => 1,
                            'whatsapp' => $request->whatsapp,
                            'email' => $request->email,
                            'username' => $userhs->username,
                            'password' => $userhs->value,
                            'trx_no' => $trxNo,
                            'virtual_account' => $response['vaNumber'],
                            'bank' => $request->payment_method,
                            'bank_name' => $request->bank_name,
                            'reference' => $response['reference'],
                            'callback_url' => $transaction['callbackUrl']
                        ];
                        $created = BillingHotspot::create($dataInsert);
                        DB::commit();
                        return [
                            'status' => true,
                            'message' => 'Virtual Account Generated',
                            'data' => $response,
                            'panduan' => get_panduan($request->payment_method)
                        ];
                    }
                    return $response;
                }
                return [
                    'status' => false,
                    'message' => 'Wrong api key',
                    'data' => []
                ];
            }
            return [
                'status' => false,
                'message' => 'Wrong api key',
                'data' => []
            ];

        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json([
                'data' => null,
                'message' => 'Something Wrong !!'
            ], 500);
        }
    }

    public function handleAdminNotification(array $requestData)
    {
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $merchantCode = $requestData['merchantCode'];
        $amount       = $requestData['amount'];
        $orderId      = $requestData['merchantOrderId'];
        $productDetail = $requestData['productDetail'];
        $additionalParam = $requestData['additionalParam'];
        $paymentCode = $requestData['paymentCode'];
        $resultCode = $requestData['resultCode'];
        $merchantUserId = $requestData['merchantUserId'];
        $reference = $requestData['reference'];
        // MD5(merchantcode + amount + merchantOrderId + merchantKey)
        $signature = $requestData['signature'];
        $publisherOrderId = $requestData['publisherOrderId'];
        $spUserHash = $requestData['spUserHash'] ?? '';
        $settlementDate = $requestData['settlementDate'] ?? '';
        $issuerCode = $requestData['issuerCode'] ?? '';

        $billing = BillingHotspot::where('trx_no', $orderId)->first();
        $signatureKey = md5($merchantCode . $amount . $orderId . $duitku->api_key);

        if ($billing && $resultCode == '00' && $billing->status === 1) {
            return $billing;
        }

        return null;
    }

    public function handleDuitkuNotification(Request $requestData)
    {
        DB::beginTransaction();
        try{
            $billing = $this->handleAdminNotification($requestData);
            if ($billing && $billing->status === 1) {
                // cari user_hr berdasakan profile yang belum terbeli
                $userhs = HotspotUser::query()->where('profile', $billing->name)
                                    ->where('statusPayment', 1)->first();

                $billing->update([
                    'status' => 2,
                    'username' => $userhs->username,
                    'password' => $userhs->value
                ]);
                // proses kirim email dan whatsapp data
                return $billing;
            }

            return null;
        }catch (\Exception $ex){

        }
    }

    public function callback(Request $request)
    {
        // Ambil raw input (format form-urlencoded)
        $raw = file_get_contents('php://input');
        Log::info($request);

        // Ubah menjadi array key => value
        parse_str($raw, $notif);
        // Logging isi callback
//          \Log::info('✅ Callback apibilling duitku:', $notif);
        // Ambil order_id dari merchantOrderId
        $order_id = $request->merchantOrderId ?? null;
        if (substr($order_id, 0, 2) === 'DN') {
            $billing = BillingHotspot::where('trx_no', $order_id)->first();
            if($billing){
                $duitku = Mduitku::where('shortname', 'dinetkan')->first();

                $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
                // false for production mode
                // true for sandbox mode
                $duitkuConfig->setSandboxMode(false);
                // set sanitizer (default : true)
                $duitkuConfig->setSanitizedMode(false);
                // set log parameter (default : true)
                $duitkuConfig->setDuitkuLogs(false);
                if ($request->resultCode == '00') {
                    // cari user_hr berdasakan profile yang belum terbeli
                    $billing->update([
                        'status' => 2
                    ]);

                    $nominal = number_format($billing->price, 0, ',', '.');
                    $template = "Pembelian Produk {$billing->name_hotspot_profile} dengan nomor transaski {$billing->trx_no} senilai Rp {$nominal} telah kami terima.<br>Berikut data login<br>Username : {$billing->username}<br>Password : {$billing->password}<br><br><br>Terima kasih atas pembeliannya.";
                    $message_format = str_replace('<br>', "\n", $template);
                    $message_format .= "\n\n\nPT Putra Garsel Interkoneksi\nJalan Asia-Afrika No.114-119 Wisma Bumi Putera Lt.3 Suite .301 B\nKb. Pisang, Kec. Sumur Bandung, Kota Bandung, Jawa Barat 40112\n+62 822-473-377";

                    try {
                        $user_dinetkan = User::where('shortname', 'dinetkan')->first();
                        $mpwa = UsersWhatsapp::where('user_id', $user_dinetkan->id)->first();
                        if($mpwa){
                            $nomorhp = gantiformat_hp($billing->whatsapp);
                            $_id = $user_dinetkan->whatsapp."_".env('APP_ENV');
                            $apiUrl = env('WHATSAPP_URL_NEW')."send-message/".$_id; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                            $params = array(
                                "jid" => $nomorhp."@s.whatsapp.net",
                                "content" => array(
                                    "text" => $message_format
                                )
                            );

                            $response = Http::timeout(20)->post($apiUrl, $params);
                        }
                        $this->send_email_pembelian($template);
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                    ]);
                    echo 'Transaction order_id: ' . $order_id . ' successfully transfered using ' . $type;
                } elseif ($notif['resultCode'] == '01') {
//                    $this->save_duitku_log($notif, $owner, $keterangan);
                    // Action Failed
                }
            }
        }
        return response()->json(['message' => 'Callback processed successfully'], 200);
    }

    function send_email_pembelian($message){
        try{
            $smtp = SmtpSetting::firstWhere('shortname', 'dinetkan');

            $data = [
                'messages' => $message,
                'notification' => 'Informasi Invoice',
            ];
            app(CustomMailerService::class)->sendWithUserSmtpCron(
                'emails.generate_invoice_billing',
                $data,
                'saeful.arifin150@gmail.com',
                "Pembelian Voucher Hotspot",
                $smtp,
                null
            );
            Log::info("Email Pembelian Voucher Hotspot Berhasil");
        }catch (\Exception $e){
            Log::error("Email Pembelian Voucher Hotspot gagal " . $e->getMessage());
        }
    }

    public function generate_va_api_pppoe(Request $request, SiteDinetkanSettings $setting){
        $generate_va = $this->generate_va_pppoe($request,$setting);
        return [
            'status' => true,
            'message' => 'Virtual Account Generated',
            'data' => $generate_va,
            'panduan' => get_panduan($request->payment_method)
        ];
    }

    public function generate_va_pppoe(Request $request, SiteDinetkanSettings $setting){
        $invoice = \App\Models\Invoice\Invoice::where('id', $request->invoice_id)->with('rpppoe')->first();
        // otc
        $amount_ppn_otc = 0;
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
        $invoice instanceof Invoice => $invoice->rpppoe->full_name,
            $invoice instanceof Invoice => $invoice->rpppoe->full_name,
            default => '',
        };
        $email = match (true) {
        $invoice instanceof Invoice => $invoice->rpppoe->email ?? '',
            $invoice instanceof Invoice => $invoice->rpppoe->email ?? '',
            default => '',
        };
        $phoneNumber = match (true) {
        $invoice instanceof Invoice => $invoice->rpppoe->wa ?? '',
            $invoice instanceof Invoice => $invoice->rpppoe->whatsapp ?? '',
            default => '',
        };

        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
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
            'callbackUrl'     => route('duitku.callback'), // url for callback
            'returnUrl'       => route('bayar.invoice', ['id' => $invoice->no_invoice]), // <- ID wajib dimasukkan
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
