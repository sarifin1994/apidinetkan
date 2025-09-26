<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\DuitkuLog;
use App\Models\Setting\MduitkuOwner;
use App\Models\Setting\MidtransOwner;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use App\Models\Pppoe\PppoeUser;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Setting\Company;
use App\Models\Setting\Mduitku;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use Carbon\Carbon;
use AdityaDarma\LaravelDuitku\Facades\DuitkuAPI;
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use App\Models\Keuangan\Transaksi;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Partnership\Mitra;
use App\Models\User;
use App\Models\Setting\WaServer;
use Illuminate\Support\Facades\Http;
use App\Models\Setting\Midtrans;

class DuitkuOwnerController extends Controller
{
    public function index()
    {
        $midtrans = MidtransOwner::where('shortname', multi_auth()->shortname)->first();
        
        return view('backend.setting.midtrans.index', compact('midtrans'));
    }
    public function store(Request $request)
    {
        $duitku = MduitkuOwner::where('shortname', multi_auth()->shortname)->exists();
        if (!$duitku) {
            $duitku = MduitkuOwner::create([
                'shortname' => multi_auth()->shortname,
                'status' => 0,
            ]);
        } else {
            if ($request->status_duitku === '1') {
                $midtrans = MidtransOwner::where('shortname', multi_auth()->shortname);
                $midtrans->update([
                    'status' => 0,
                ]);
            }
            $duitku = MduitkuOwner::where('shortname', multi_auth()->shortname);
            $duitku->update([
                'id_merchant' => $request->merchant_code,
                'api_key' => $request->api_key,
                'admin_fee' => $request->admin_fee_duitku,
                'status' => $request->status_duitku,
                'environment' => $request->environment,
                'url_production' => $request->url_production,
                'url_development' => $request->url_development,
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $duitku,
        ]);
    }
    public function bayar()
    {
        function tgl_indo($tanggal)
        {
            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            $pecahkan = explode('-', $tanggal);
            return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
        }
        $no_invoice = last(request()->segments(2));
        $invoice = Invoice::where('no_invoice', $no_invoice)->with('rpppoe')->first();
        if (!$invoice) {
            return view('backend.invoice.404');
        } else {
            $ppp = PppoeUser::where('id', $invoice->rpppoe->id)->first();
            $company = Company::where('shortname', $invoice->shortname)->first();
            $get_periode = date('Y-m-d', strtotime($invoice->period));
            $periode_format = tgl_indo($get_periode);
            return view('backend.invoice.pay.index_duitku', compact('company', 'invoice', 'ppp', 'periode_format'));
        }
    }

    public function create(Request $request)
    {
        $invoice = Invoice::where('id', $request->invoice_id)->with('rpppoe')->first();
        $duitku = MduitkuOwner::where('shortname', $invoice->shortname)->first();
        $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
        // true for sandbox mode
        $duitkuConfig->setSandboxMode(false);
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(false);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(false);

        $amount_ppn = $invoice->price * ($invoice->ppn / 100);
        if ($invoice->discount > 0) {
            $amount_discount = $invoice->discount;
        } else {
            $amount_discount = 0;
        }

        // $paymentMethod      = ""; // PaymentMethod list => https://docs.duitku.com/pop/id/#payment-method
        // bebankan ke pelanggan
        // $paymentAmount = (int) $invoice->price + (int) +(int) $amount_ppn - (int) $amount_discount; // no decimal allowed for creditcard
        $phoneNumber = $invoice->rpppoe->wa; // your customer phone number (optional)
        $productDetails = $invoice->item;
        $merchantOrderId = $invoice->no_invoice;
        // $merchantOrderId = '911121173';
        $customerVaName = $invoice->rpppoe->full_name; // display name on bank confirmation display
        $callbackUrl = route('duitku.callback'); // url for callback
        $returnUrl = route('bayar.invoice', ['id' => $invoice->no_invoice]); // <- ID wajib dimasukkan
        $expiryPeriod = 60; // set the expired time in minutes

        // Customer Detail
        $firstName = $invoice->rpppoe->full_name;

        // Address
        $alamat = $invoice->rpppoe->address;

        $address = [
            'firstName' => $firstName,
            'address' => $alamat,
            'phone' => $phoneNumber,
        ];

        $customerDetail = [
            'firstName' => $firstName,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
        ];

        $item1 = [
            'name' => $productDetails,
            'price' => (int) $invoice->price,
            'quantity' => 1,
        ];

        $itemDetails = [$item1];

        $totalItemPrice = (int) $invoice->price;

        if ($amount_ppn > 0) {
            $itemDetails[] = [
                'id' => 'tax',
                'price' => (int) $amount_ppn,
                'quantity' => 1,
                'name' => 'PPN ' . $invoice->ppn . '%',
            ];
            $totalItemPrice += (int) $amount_ppn;
        }

        if ($amount_discount > 0) {
            $itemDetails[] = [
                'id' => 'discount',
                'price' => (int) -$amount_discount,
                'quantity' => 1,
                'name' => 'Discount',
            ];
            $totalItemPrice -= (int) $amount_discount;
        }

        $paymentAmount = $totalItemPrice;

        $params = [
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'customerVaName' => $customerVaName,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => $expiryPeriod,
        ];

        try {
            // createInvoice Request
            $responseDuitkuPop = \Duitku\Pop::createInvoice($params, $duitkuConfig);
            header('Content-Type: application/json');
            $response = json_decode($responseDuitkuPop, true);
            return response()->json($response);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function callback(Request $request)
    {
        // Ambil raw input (format form-urlencoded)
        $raw = file_get_contents('php://input');
//        Log::info($request);

        // Ubah menjadi array key => value
        parse_str($raw, $notif);
        // Logging isi callback
        //  \Log::info('✅ Callback diterima dari Duitku:', $notif);
        // Ambil order_id dari merchantOrderId
        $order_id = $request->merchantOrderId ?? null;
        if (substr($order_id, 0, 2) === 'RQ') {
            $user = User::where('order_number', $order_id)->first();
            if($user){
                $duitku = MduitkuOwner::first();
                $owner = User::query()->where('shortname', $duitku->shortname)->first();
                $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
                // false for production mode
                // true for sandbox mode
                $duitkuConfig->setSandboxMode(false);
                // set sanitizer (default : true)
                $duitkuConfig->setSanitizedMode(false);
                // set log parameter (default : true)
                $duitkuConfig->setDuitkuLogs(false);

//                $type = $notif['paymentCode'];
//                $total_duitku = $notif['amount'];

                $keterangan = "Pembayaran oleh ".$user->name ." untuk ".$user->license->name;
                if ($request->resultCode == '00') {
                    function tgl_indo($tanggal)
                    {
                        $bulan = [
                            1 => 'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Juli',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                        ];
                        $pecahkan = explode('-', $tanggal);
                        return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
                    }
                    $user_update = User::where('shortname', $user->shortname)->first();// trial
                    if ($user->license_id == 1 && $user->order == null) {
                        $paid_date = Carbon::today();
                        $next_due = Carbon::parse($paid_date)->copy()->addMonth();
                        $user_update->update([
                            'status' => 1,
                            'license_id' => $user->order,
                            'next_due' => $next_due,
                            'order' => null,
                            'order_status' => 'paid',
                        ]);
                    }else{
                        $next_due = Carbon::parse($user->next_due)->copy()->addMonth();
                        $user_update->update([
                            'status' => 1,
                            'license_id' => $user->order,
                            'next_due' => $next_due,
                            'order' => null,
                            'order_status' => 'paid',
                        ]);
                    }
                    $this->save_duitku_log($notif, $owner, $keterangan);
                    $user = User::where('order_number', $order_id)->first();
                    $nominal = number_format($user->license->price, 0, ',', '.');
                    $domain = env('APP_URL');
                    $template = "
👋 Hai, *{$user->name}*

Pembayaran lisensi `{$user->license->name}` dengan nomor `{$user->order_number}` senilai `Rp {$nominal}` telah kami terima.

Silakan login ke dashboard `{$domain}` untuk mengecek status akunmu.

Terima kasih atas perhatian dan kerjasamanya.

Salam hormat,
*Radiusqu*";
                    $message_format = str_replace('<br>', "\n", $template);

                    // ambil server pertama
                    $wa_server = Mpwa::where('shortname', 'owner_radiusqu')->first();
                    try {
                        $curl = curl_init();
                        $data = [
                            'api_key' => $wa_server->api_key,
                            'sender' => $wa_server->sender,
                            'number' => $user->whatsapp,
                            'message' => $message_format,
                        ];
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($curl, CURLOPT_URL, 'https://' . $wa_server->mpwa_server . '/send-message');
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        // $result = json_decode($response, true);
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                    ]);
                    echo 'Transaction order_id: ' . $order_id . ' successfully transfered using ' . $type;
                } elseif ($notif['resultCode'] == '01') {
                    $this->save_duitku_log($notif, $owner, $keterangan);
                    // Action Failed
                }
            }
        }
        return response()->json(['message' => 'Callback processed successfully'], 200);
    }

    // function getDuitkuPaymentFees($merchantCode, $apiKey, $amount = 10000)
    // {
    //     $datetime = date('Y-m-d H:i:s');
    //     $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);

    //     $params = [
    //         'merchantcode' => $merchantCode,
    //         'amount' => $amount,
    //         'datetime' => $datetime,
    //         'signature' => $signature,
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod');
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($params))]);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);

    //     if ($httpCode === 200) {
    //         $result = json_decode($response, true);
    //         $feeMap = [];

    //         foreach ($result['paymentFee'] as $fee) {
    //             $paymentCode = $fee['paymentMethod'];
    //             $value = $fee['totalFee']['value'] ?? 0;
    //             $type = $fee['totalFee']['type'] ?? 'flat'; // 'flat' or 'percent'

    //             $feeMap[$paymentCode] = [
    //                 'type' => strtolower($type),
    //                 'value' => $value,
    //             ];
    //         }

    //         return $feeMap;
    //     }

    //     return [];
    // }


    function save_duitku_log($notif, $user, $keterangan = ""){
        try{
            $data=[
                'shortname'         => $user->shortname,
                'apiKey'            => $notif['apiKey'] ?? '',
                'merchantCode'      => $notif['merchantCode'] ?? '',
                'amount'            => $notif['amount'] ?? '',
                'merchantOrderId'   => $notif['merchantOrderId'] ?? '',
                'productDetail'     => $notif['productDetail'] ?? '',
                'additionalParam'   => $notif['additionalParam'] ?? '',
                'paymentMethod'     => $notif['paymentCode'] ?? '',
                'resultCode'        => $notif['resultCode'] ?? '',
                'merchantUserId'    => $notif['merchantUserId'] ?? '',
                'reference'         => $notif['reference'] ?? '',
                'signature'         => $notif['signature'] ?? '',
                'publisherOrderId'  => $notif['publisherOrderId'] ?? '',
                'spUserHash'        => $notif['spUserHash'] ?? '',
                'settlementDate'    => $notif['settlementDate'] ?? '',
                'issuerCode'        => $notif['issuerCode'] ?? '',
                'vaNumber'          => $notif['vaNumber'] ?? '',
                'notes'             => $keterangan
            ];
            DuitkuLog::create($data);
        }catch (\Exception $e){
            Log::info('error save log '.$e->getMessage());
        }
    }
}
