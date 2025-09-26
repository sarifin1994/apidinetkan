<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\BillingService;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DuitkuController extends Controller
{
    public function index()
    {
        $midtrans = Midtrans::where('shortname', multi_auth()->shortname)->first();
        if (multi_auth()->license_id == 2) {
            return view('backend.account.limit');
        } else {
            return view('backend.setting.midtrans.index', compact('midtrans'));
        }
    }
    public function store(Request $request)
    {
        $duitku = Mduitku::where('shortname', multi_auth()->shortname)->exists();
        if (!$duitku) {
            $duitku = Mduitku::create([
                'shortname' => multi_auth()->shortname,
//                'status' => 0,
                'id_merchant' => $request->merchant_code,
                'api_key' => $request->api_key,
                'admin_fee' => $request->admin_fee_duitku,
                'status' => $request->status_duitku,
                'user_id' => $request->user_id,
                'secret_key' => $request->secret_key,
                'status_widrawal' => $request->status_widrawal,
                'fee_disburs' => $request->fee_disburs,
                'email_disburs' => $request->email_disburs,
                'minimal_disburs' => $request->minimal_disburs
            ]);
        } else {
            if ($request->status_duitku === '1') {
                $midtrans = Midtrans::where('shortname', multi_auth()->shortname);
                $midtrans->update([
                    'status' => 1,
                ]);
            }
            $duitku = Mduitku::where('shortname', multi_auth()->shortname);
            $duitku->update([
                'id_merchant' => $request->merchant_code,
                'api_key' => $request->api_key,
                'admin_fee' => $request->admin_fee_duitku,
                'status' => $request->status_duitku,
                'user_id' => $request->user_id,
                'secret_key' => $request->secret_key,
                'status_widrawal' => $request->status_widrawal,
                'fee_disburs' => $request->fee_disburs,
                'email_disburs' => $request->email_disburs,
                'minimal_disburs' => $request->minimal_disburs
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
//        dd($invoice);exit;
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
        $duitku = Mduitku::where('shortname', $invoice->shortname)->first();
        $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
        // true for sandbox mode
        if(env('APP_ENV') == 'development'){
            $duitkuConfig->setSandboxMode(true);
        }
        if(env('APP_ENV') == 'production'){
            $duitkuConfig->setSandboxMode(false);
        }
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

        $adons=[];
        $total_ppn_add = 0;
        $total_price = 0;
        if(isset($invoice->rpppoe->addon)){
            $x=1;
            foreach($invoice->rpppoe->addon as $ad){
                if($ad->no_invoice == $invoice->no_invoice){
                    if($ad->ppn > 0){
                        $total_ppn_add = $total_ppn_add + ($ad->price * $ad->ppn / 100);
                    }
                    $total_price = $total_price + $ad->price + $total_ppn_add;
                    $itemDetails[]=[
                        'id' => 'Ad on '.$x,
                        'price' => (int) ($ad->price + $total_ppn_add),
                        'quantity' => 1,
                        'name' => $ad->description,
                    ];
                    $x++;
                }
            }
        }

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

        $paymentAmount = $totalItemPrice + $total_price;

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

        // Ubah menjadi array key => value
        parse_str($raw, $notif);
        // Logging isi callback
        // \Log::info('✅ Callback diterima dari Duitku:', $notif);
        // Ambil order_id dari merchantOrderId
        $order_id = $notif['merchantOrderId'] ?? null;

        // Lanjut proses invoice
        $invoice = Invoice::where('no_invoice', $order_id)->first();
        if (!$invoice) {
            return response('Invoice not found', 404);
        }

        $duitku = Mduitku::where('shortname', $invoice->shortname)->first();
        $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
        // false for production mode
        // true for sandbox mode
        $duitkuConfig->setSandboxMode(false);
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(false);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(false);

        $type = $notif['paymentCode'];
        $total_duitku = $notif['amount'];

        if ($notif['resultCode'] == '00') {
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
            $invoice = Invoice::where('no_invoice', $order_id)->first();
            if (!$invoice) {
                echo 'Invoice tidak ditemukan.';
            } else {
                $shortname = $invoice->shortname;
                $pppoe = PppoeUser::where('id', $invoice->id_pelanggan)->first();

                if ($invoice->status === 'unpaid') {
                    if ($invoice->payment_type === 'Prabayar') {
                        $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                        $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Fixed Date') {
                        $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                        $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                    } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Billing Cycle') {
                        $due_bc = BillingSetting::where('shortname', $shortname)->select('due_bc')->first();
                        $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->setDay($due_bc->due_bc)->addMonths(1);
                        $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->startOfMonth()->addMonths(1);
                    }
                    $pppoe_update = PppoeUser::where('id', $invoice->id_pelanggan);
                    $pppoe_update->update([
                        'next_due' => $next_due,
                        'next_invoice' => $next_invoice,
                    ]);

                    // Ambil data dari callback
                    $paymentCode = $type;
                    $amount = $total_duitku ?? 0;

                    // Hitung admin fee berdasarkan paymentCode
                    function calculateDuitkuAdminFee($paymentCode, $amount)
                    {
                        $flatFees = [
                            'BC' => 5000, // BCA
                            'I1' => 3000, // BNI
                            'BR' => 3000, // BRI
                            'M2' => 3000, // Mandiri
                            'VA' => 3000, // Maybank
                            'B1' => 3000, // CIMB Niaga
                            'BT' => 3000, // Permata
                            'BV' => 3000, // BSI
                            'AG' => 3000, // Artha Graha
                            'NC' => 5000, // BNC
                            'FT' => 7500, // Alfamart, Pos Indonesia, Pegadaian
                            'IR' => 6000, // INDOMARET
                        ];

                        $percentFees = [
                            'OV' => 1.5,
                            'SL' => 0.7,
                        ];

                        if (isset($flatFees[$paymentCode])) {
                            return $flatFees[$paymentCode];
                        }

                        if (isset($percentFees[$paymentCode])) {
                            return round(($amount * $percentFees[$paymentCode]) / 100);
                        }

                        return 0;
                    }

                    // Hitung fee
                    $admin_duitku = calculateDuitkuAdminFee($paymentCode, $amount);

                    // dibebankan ke merchant
                    if ($duitku->admin_fee === 0) {
                        $admin_fee = $admin_duitku;
                    } else {
                        $admin_fee = 0;
                    }

                    $fee_mitra = PppoeProfile::where('shortname', $shortname)->where('id', $pppoe->profile_id)->first()->fee_mitra;
                    $transaksi = Transaksi::create([
                        'shortname' => $shortname,
                        'id_data' => $invoice->id,
                        'tipe' => 'Pemasukan',
                        'kategori' => 'Invoice',
                        'deskripsi' => "Payment #$invoice->no_invoice a.n $pppoe->full_name",
                        'nominal' => $total_duitku - $fee_mitra - $admin_fee,
                        'tanggal' => Carbon::now(),
                        'metode' => $type,
                        'created_by' => 'duitku',
                    ]);

                    if ($invoice->mitra_id != 0) {
                        $nama_mitra = Mitra::where('shortname', $shortname)->where('id', $invoice->mitra_id)->first()->name;
                        $transaksi = TransaksiMitra::create([
                            'shortname' => $shortname,
                            'mitra_id' => $invoice->mitra_id,
                            'id_data' => $invoice->id,
                            'tanggal' => Carbon::now(),
                            'tipe' => 'Pemasukan',
                            'kategori' => 'Komisi',
                            'deskripsi' => "Komisi $nama_mitra #$invoice->no_invoice a.n $pppoe->full_name",
                            'nominal' => $fee_mitra,
                            'metode' => $type,
                            'created_by' => 'duitku',
                        ]);

                        $balancehistory = Balancehistory::create([
                            'id_mitra' => $transaksi->mitra_id,
                            'id_reseller' => '',
                            'tx_amount' => $transaksi->nominal,
                            'notes' => $transaksi->deskripsi,
                            'type' => 'in',
                            'tx_date' => Carbon::now(),
                            'id_transaksi' => $transaksi->id
                        ]);

                        $updatemitra = Mitra::where('id', $invoice->mitra_id)->first();
                        if($updatemitra){
                            $lastbalance = $updatemitra->balance;
                            $updatemitra->update([
                                'balance' => $lastbalance + (int)$transaksi->nominal
                            ]);
                        }
                    }

                    $invoice_update = Invoice::where('no_invoice', $order_id);
                    $invoice_update->update([
                        'paid_date' => Carbon::today()->toDateString(),
                        'status' => 'paid',
                    ]);

                    $notif_ps = BillingSetting::where('shortname', $shortname)->first()->notif_ps;
                    if ($notif_ps === 1 && $pppoe->wa !== null) {
                        $amount_format = number_format($invoice->amount, 0, '.', '.');
                        $total_format = number_format($total_duitku, 0, '.', '.');
                        $invoice_date_format = date('d/m/Y', strtotime($invoice->invoice_date));
                        $due_date_format = date('d/m/Y', strtotime($invoice->due_date));
                        $get_periode = date('Y-m-d', strtotime($invoice->period));
                        $periode_format = tgl_indo($get_periode);
                        $mpwa = Mpwa::where('shortname', $shortname)->first();
                        $paid_date_format = date('d/m/Y', strtotime($invoice->paid_date));
                        $row = PppoeUser::where('id', $invoice->id_pelanggan)->first();
                        // $inv = Invoice::where('id',$request->invoice_id)->first();
                        $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]', '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]', '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[tgl_bayar]', '[subscribe]', '[metode_pembayaran]'];
                        $source = [$row->full_name, $row->id_pelanggan, $row->username, $row->value, $row->address, $row->profile, $invoice->payment_type, $invoice->billing_period, $invoice->no_invoice, $invoice_date_format, $amount_format, $invoice->ppn, $invoice->discount, $total_format, $due_date_format, $periode_format, $paid_date_format, $invoice->subscribe, $type];
                        $template = Watemplate::where('shortname', $row->shortname)->first()->payment_paid;
                        $message = str_replace($shortcode, $source, $template);
                        $message_format = str_replace('<br>', "\n", $message);

                        try {
                            $curl = curl_init();
                            $data = [
                                'api_key' => $mpwa->api_key,
                                'sender' => $mpwa->sender,
                                'number' => $row->wa,
                                'message' => $message_format,
                            ];
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                            curl_setopt($curl, CURLOPT_URL, 'https://' . $mpwa->mpwa_server . '/send-message');
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                            $response = curl_exec($curl);
                            curl_close($curl);
                            // $result = json_decode($response, true);
                        } catch (\Exception $e) {
                            return $e->getMessage();
                        }
                    }

                    $cek_inv = Invoice::where('id_pelanggan', $invoice->id_pelanggan)->where('status', 'unpaid')->count();
                    if ($pppoe->status === 2 && $cek_inv === 0) {
                        $ssh_user = env('IP_RADIUS_USERNAME');
                        $ssh_host = env('IP_RADIUS_SERVER');
                        $ppp = PppoeUser::where('id', $invoice->id_pelanggan);
                        $ppp->update([
                            'status' => 1,
                        ]);
                        if ($pppoe->nas === null) {
                            $nas = RadiusNas::where('shortname', $shortname)->select('nasname', 'secret')->get();
                            foreach ($nas as $row) {
                                $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                                $command = "echo $userAttr | radclient -r 1 {$row['nasname']}:3799 disconnect {$row['secret']}";
                                $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                            }
                        } else {
                            $secret = RadiusNas::where('shortname', $shortname)->where('nasname', $pppoe->nas)->select('secret')->first();
                            $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                            $command = "echo $userAttr | radclient -r 1 {$pppoe->nas}:3799 disconnect {$secret->secret}";
                            $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                            $process = Process::run($ssh_command);
                        }
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Invoice Berhasil Dibayar',
                    ]);
                }
            }
            echo 'Transaction order_id: ' . $order_id . ' successfully transfered using ' . $type;
        } elseif ($notif['resultCode'] == '01') {
            // Action Failed
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

    public function create_setoran(Request $request)
    {
        $billingservice = BillingService::where('id', $request->setoran_id)->first();
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
        $user = User::where('dinetkan_user_id', $billingservice->dinetkan_user_id)->first();
        // true for sandbox mode
        if(env('APP_ENV') == 'development'){
            $duitkuConfig->setSandboxMode(true);
        }
        if(env('APP_ENV') == 'production'){
            $duitkuConfig->setSandboxMode(false);
        }
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(false);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(false);

        // $paymentMethod      = ""; // PaymentMethod list => https://docs.duitku.com/pop/id/#payment-method
        // bebankan ke pelanggan
        // $paymentAmount = (int) $invoice->price + (int) +(int) $amount_ppn - (int) $amount_discount; // no decimal allowed for creditcard
        $phoneNumber = $user->whatsapp; // your customer phone number (optional)
        $productDetails = "Periode : ".$billingservice->month." ".$billingservice->year;
        $merchantOrderId = "#setoran".$billingservice->id;
        $customerVaName = $user->name; // display name on bank confirmation display
        $callbackUrl = route('duitku.callback_setoran'); // url for callback
        $returnUrl = route('admin.billing.member_dinetkan.mapping_service'); // <- ID wajib dimasukkan
        $expiryPeriod = 60; // set the expired time in minutes

        // Customer Detail
        $firstName = $user->name;

        // Address
        $alamat = $user->address;

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

//        $item1 = [
//            'name' => "PRICE : ".$productDetails,
//            'price' => (int) $billingservice->total_price,
//            'quantity' => 1,
//        ];

        $item2 = [
            'name' => "PPN : ".$productDetails,
            'price' => (int) $billingservice->total_ppn,
            'quantity' => 1,
        ];

        $item3 = [
            'name' => "BHP : ".$productDetails,
            'price' => (int) $billingservice->total_bhp,
            'quantity' => 1,
        ];

        $item4 = [
            'name' => "USO ".$productDetails,
            'price' => (int) $billingservice->total_uso,
            'quantity' => 1,
        ];

        $itemDetails = [$item2,$item3,$item4];

        $price = $billingservice->total_price;
        $ppn = $billingservice->total_ppn;
        $bhp = $billingservice->total_bhp;
        $uso = $billingservice->total_uso;
        $totalItemPrice = $ppn + $bhp + $uso;
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

    public function callback_setoran(Request $request)
    {
        // Ambil raw input (format form-urlencoded)
        $raw = file_get_contents('php://input');

        // Ubah menjadi array key => value
        parse_str($raw, $notif);
        // Logging isi callback
//         \Log::info('✅ Callback Setoran diterima dari Duitku:', $notif);
        // Ambil order_id dari merchantOrderId
        $order_id = $notif['merchantOrderId'] ?? null;
        $order_id = Str::replace("#setoran","",$order_id);
        // Lanjut proses invoice
        $billingservice = BillingService::where('id', $order_id)->first();
        if (!$billingservice) {
            return response('Billing Serviice not found', 404);
        }

        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $duitkuConfig = new \Duitku\Config($duitku->api_key, $duitku->id_merchant);
        $user = User::where('dinetkan_user_id', $billingservice->dinetkan_user_id)->first();
        // true for sandbox mode
        if(env('APP_ENV') == 'development'){
            $duitkuConfig->setSandboxMode(true);
        }
        if(env('APP_ENV') == 'production'){
            $duitkuConfig->setSandboxMode(false);
        }
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(false);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(false);

        $type = $notif['paymentCode'];
        $total_duitku = $notif['amount'];

        if ($notif['resultCode'] == '00') {
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
            $billingservice = BillingService::where('id', $order_id)->first();
            if (!$billingservice) {
//                Log::info('billing tidak di temukan');
                echo 'Billing Service tidak ditemukan.';
            } else {
                $shortname = $user->shortname;

                if ($billingservice->status == 'unpaid') {
//                    Log::info('masuk unpaid');
                    $billingservice->update([
                        'status' => 'paid',
                        'paid_via' => 'Transfer',
                        'paid_date' => \Carbon\Carbon::now()->format('Y-m-d H:i')
                    ]);
                    $transaksi = Transaksi::create([
//                        'shortname' => $shortname,
                        'shortname' => 'dinetkan',
                        'id_data' => $billingservice->id,
                        'tipe' => 'Pemasukan',
                        'kategori' => 'Invoice',
                        'deskripsi' => "Pembayaran Setoran ".Str::upper($shortname )." #$billingservice->month - $billingservice->year ",
                        'nominal' => $billingservice->total_price,
                        'tanggal' => Carbon::now(),
                        'metode' => $billingservice->bank_name,
                        'created_by' => 'duitku',
                    ]);

                    // Ambil data dari callback
                    $paymentCode = $type;
                    $amount = $total_duitku ?? 0;

                    // Hitung admin fee berdasarkan paymentCode
                    function calculateDuitkuAdminFee($paymentCode, $amount)
                    {
                        $flatFees = [
                            'BC' => 5000, // BCA
                            'I1' => 3000, // BNI
                            'BR' => 3000, // BRI
                            'M2' => 3000, // Mandiri
                            'VA' => 3000, // Maybank
                            'B1' => 3000, // CIMB Niaga
                            'BT' => 3000, // Permata
                            'BV' => 3000, // BSI
                            'AG' => 3000, // Artha Graha
                            'NC' => 5000, // BNC
                            'FT' => 7500, // Alfamart, Pos Indonesia, Pegadaian
                            'IR' => 6000, // INDOMARET
                        ];

                        $percentFees = [
                            'OV' => 1.5,
                            'SL' => 0.7,
                        ];

                        if (isset($flatFees[$paymentCode])) {
                            return $flatFees[$paymentCode];
                        }

                        if (isset($percentFees[$paymentCode])) {
                            return round(($amount * $percentFees[$paymentCode]) / 100);
                        }

                        return 0;
                    }

                    // Hitung fee
                    $admin_duitku = calculateDuitkuAdminFee($paymentCode, $amount);

                    // dibebankan ke merchant
                    if ($duitku->admin_fee === 0) {
                        $admin_fee = $admin_duitku;
                    } else {
                        $admin_fee = 0;
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Invoice Berhasil Dibayar',
                    ]);
                }
            }
            echo 'Transaction order_id: ' . $order_id . ' successfully transfered using ' . $type;
        } elseif ($notif['resultCode'] == '01') {
            // Action Failed
        }
        return response()->json(['message' => 'Callback processed successfully'], 200);
    }

    public function get_payment_method()
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
}
