<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use App\Models\Pppoe\PppoeUser;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Setting\Company;
use App\Models\Setting\Midtrans;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Process;
use App\Models\Keuangan\Transaksi;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Partnership\Mitra;
use App\Models\User;
use App\Models\Setting\Mduitku;

class MidtransController extends Controller
{
    public function update(Request $request, Midtrans $midtran)
    {
        if($request->status === '1'){
            $duitku = Mduitku::where('shortname',multi_auth()->shortname);
            $duitku->update([
                'status' => 0,
            ]);
        }
        $midtran->update([
            'id_merchant' => $request->id_merchant,
            'client_key' => $request->client_key,
            'server_key' => $request->server_key,
            'admin_fee' => $request->admin_fee,
            'status' => $request->status,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $midtran,
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

            $midtrans = Midtrans::where('shortname', $invoice->shortname)->first();
            Config::$serverKey = $midtrans->server_key;
            $client_key = Config::$clientKey = $midtrans->client_key;
            if ($midtrans->status == 0) {
                Config::$isProduction = false;
            } else {
                Config::$isProduction = true;
            }
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $amount_ppn = $invoice->price * ($invoice->ppn / 100);

            if ($invoice->discount > 0) {
                $amount_discount = $invoice->discount;
            } else {
                $amount_discount = 0;
            }
            // Required
            $adons=[];
            $total_ppn_add = 0;
            $total_price = 0;
            if(isset($invoice->rpppoe->addon)){
                $x=1;
                foreach($invoice->rpppoe->addon as $ad){
                    if($ad->ppn > 0){
                        $total_ppn_add = total_ppn_add + ($ad->price * $ad->ppn / 100);
                    }
                    $total_price = $total_price + $ad->price + $total_ppn_add;
                    $adons[]=[
                        'id' => 'Ad on '.$x,
                        'price' => (int) $ad->price + $total_ppn_add,
                        'quantity' => 1,
                        'name' => $ad->description,
                    ];
                    $x++;
                }
            }
            $transaction_details = [
                'order_id' => $invoice->no_invoice,
                'gross_amount' => (int) $invoice->price + (int) $midtrans->admin_fee + (int) $amount_ppn - (int) $amount_discount + (int) $total_price, // no decimal allowed for creditcard
            ];
            // dd($transaction_details);
            $customer_details = [
                'first_name' => $invoice->rpppoe->full_name,
                'phone' => $invoice->rpppoe->wa,
                'billing_address' => $invoice->rpppoe->address,
                // 'shipping_address' => $invoice->rpppoe->address,
            ];
            $item_details = [
                [
                    'id' => 'item',
                    'price' => (int) $invoice->price,
                    'quantity' => 1,
                    'name' => $invoice->item,
                ],
                [
                    'id' => 'tax',
                    'price' => (int) $amount_ppn,
                    'quantity' => 1,
                    'name' => 'PPN ' . $invoice->ppn . '%',
                ],
                [
                    'id' => 'discount',
                    'price' => (int) -$amount_discount,
                    'quantity' => 1,
                    'name' => 'Discount',
                ],
                [
                    'id' => 'admin_fee',
                    'price' => (int) $midtrans->admin_fee,
                    'quantity' => 1,
                    'name' => 'Biaya Admin',
                ],
            ];
            // dd($item_details);

            $transaction = [
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => array_merge($item_details,$adons) ,
            ];
             dd($transaction);

            $snap_token = $invoice->snap_token;
            if ($invoice->snap_token === null) {
                try {
                    $snap_token = Snap::getSnapToken($transaction);
                    Invoice::where('no_invoice', $invoice->no_invoice)->update(['snap_token' => $snap_token]);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
            return view('backend.invoice.pay.index', compact('company', 'invoice', 'periode_format', 'midtrans', 'snap_token', 'client_key', 'ppp'));
        }
    }

    public function notification(Request $request)
    {
        if (substr($request->order_id, 0, 2) === 'RQ') {
            $user = User::where('order_number', $request->order_id)->first();
            if ($user) {
                $server_key = env('SERVER_MIDTRANS');
                $client_key = env('CLIENT_MIDTRANS');
                $midtrans_status = env('STATUS_MIDTRANS');

                if ($server_key !== null) {
                    Config::$serverKey = $server_key;
                    $client_key = Config::$clientKey = $client_key;
                    if ($midtrans_status === 'Production') {
                        Config::$isProduction = true;
                    } else {
                        Config::$isProduction = false;
                    }
                    try {
                        $notif = new Notification();
                    } catch (\Exception $e) {
                        exit($e->getMessage());
                    }
                    $notif = $notif->getResponse();
                    $transaction = $notif->transaction_status;
                    $type = $notif->payment_type;
                    $order_id = $notif->order_id;
                    $fraud = $notif->fraud_status;
                    $total_midtrans = (int)$notif->gross_amount;

                    if ($transaction == 'capture') {
                        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                        if ($type == 'credit_card') {
                            if ($fraud == 'challenge') {
                                echo 'Transaction order_id: ' . $order_id . ' is challenged by FDS';
                            } else {
                                echo 'Transaction order_id: ' . $order_id . ' successfully captured using ' . $type;
                            }
                        }
                    } elseif ($transaction == 'settlement') {
                        $user_update = User::where('shortname', $user->shortname);
                        
                        // trial
                        if ($user->license_id == 1) {
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
                        $user = User::where('order_number', $request->order_id)->first();

                        $nominal = number_format($user->license->price, 0, ',', '.');
                        $app_url = env('APP_URL');
                        $template = <<<MSG
                        👋 Hai, *{$user->username}*
                        
                        Pembayaran lisensi `{$user->license->name}` dengan nomor `{$user->order_number}` senilai `Rp {$nominal}` telah kami terima.

                        Silakan login ke dashboard `{$app_url}` untuk mengecek status akunmu.

                        Terima kasih atas perhatian dan kerjasamanya.

                        Salam hormat,
                        *Radiusqu*
                        MSG;
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
                    } else {
                        echo 'User tidak ditemukan.';
                    }
                    echo 'Transaction order_id: ' . $order_id . ' successfully transfered using ' . $type;
                } elseif ($transaction == 'pending') {
                    echo 'Waiting customer to finish transaction order_id: ' . $order_id . ' using ' . $type;
                } elseif ($transaction == 'deny') {
                    echo 'Payment using ' . $type . ' for transaction order_id: ' . $order_id . ' is denied.';
                } elseif ($transaction == 'expire') {
                    echo 'Sorry your transaction is expired';
                } elseif ($transaction == 'cancel') {
                    // TODO set payment status in merchant's database to 'Denied'
                    echo 'Payment using ' . $type . ' for transaction order_id: ' . $order_id . ' is canceled.';
                }
            }
        } else {
            $invoice = Invoice::where('no_invoice', $request->order_id)->first();
            if (!$invoice) {
                echo 'Invoice tidak ditemukan.';
            } else {
                $shortname = $invoice->shortname;
                $midtrans = Midtrans::where('shortname', $shortname)->first();
                if ($midtrans->server_key !== null) {
                    Config::$serverKey = $midtrans->server_key;
                    if ($midtrans->status == 0) {
                        Config::$isProduction = false;
                    } else {
                        Config::$isProduction = true;
                    }
                    try {
                        $notif = new Notification();
                    } catch (\Exception $e) {
                        exit($e->getMessage());
                    }
                    $notif = $notif->getResponse();
                    $transaction = $notif->transaction_status;
                    $type = $notif->payment_type;
                    $order_id = $notif->order_id;
                    $fraud = $notif->fraud_status;
                    $total_midtrans = (int)$notif->gross_amount;

                    if ($transaction == 'capture') {
                        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                        if ($type == 'credit_card') {
                            if ($fraud == 'challenge') {
                                echo 'Transaction order_id: ' . $order_id . ' is challenged by FDS';
                            } else {
                                echo 'Transaction order_id: ' . $order_id . ' successfully captured using ' . $type;
                            }
                        }
                    } elseif ($transaction == 'settlement') {
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
                                if ($invoice->payment_type === 'Prabayar' && $invoice->billing_period === 'Fixed Date') {
                                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Fixed Date') {
                                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->addMonthsWithNoOverflow(1);
                                } elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Billing Cycle') {
                                    $due_bc = BillingSetting::where('shortname', $shortname)->select('due_bc')->first();
                                    $next_due = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->setDay($due_bc->due_bc)->addMonths(1);
                                    $next_invoice = Carbon::createFromFormat('Y-m-d', $invoice->due_date)->startOfMonth()->addMonths(1);
                                } elseif ($invoice->payment_type === 'Prabayar' && $invoice->billing_period === 'Renewable') {
                                    $due_date = Carbon::createFromFormat('Y-m-d', $invoice->due_date);
                                    $pay_date = Carbon::now(); // misalnya ini adalah tanggal pembayaran (bisa juga $request->pay_date kalau ada)
                                    if ($due_date->lessThan($pay_date)) {
                                        // Jika due_date lebih kecil dari hari ini, set berdasarkan tanggal bayar
                                        $next_due = $pay_date->copy()->addMonthsWithNoOverflow(1);
                                        $next_invoice = $pay_date->copy()->addMonthsWithNoOverflow(1);
                                    }else{
                                        $next_due = $due_date->copy()->addMonthsWithNoOverflow(1);
                                        $next_invoice = $due_date->copy()->addMonthsWithNoOverflow(1);
                                    }
                                }  

                                $pppoe_update = PppoeUser::where('id', $invoice->id_pelanggan);
                                $pppoe_update->update([
                                    'next_due' => $next_due,
                                    'next_invoice' => $next_invoice,
                                ]);

                                if ($type == 'qris') {
                                    $fee_midtrans = $total_midtrans * (2 / 100);
                                    // $fee_midtrans = $total_midtrans * (0.7 / 100);
                                } elseif ($type == 'bank_transfer') {
                                    $fee_midtrans = 4400;
                                } else {
                                    $fee_midtrans = 0;
                                }

                                $fee_mitra = PppoeProfile::where('shortname', $shortname)->where('id', $pppoe->profile_id)->first()->fee_mitra;

                                $transaksi = Transaksi::create([
                                    'shortname' => $shortname,
                                    'id_data' => $invoice->id,
                                    'tipe' => 'Pemasukan',
                                    'kategori' => 'Invoice',
                                    'deskripsi' => "Payment #$invoice->no_invoice a.n $pppoe->full_name",
                                    'nominal' => $total_midtrans - $fee_midtrans - $fee_mitra,
                                    'tanggal' => Carbon::now(),
                                    'metode' => $type,
                                    'created_by' => 'midtrans',
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
                                        'created_by' => 'midtrans',
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
                                    $total_format = number_format($total_midtrans, 0, '.', '.');
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
                                    $sshOptions = ['-o', 'BatchMode=yes', '-o', 'StrictHostKeyChecking=no'];
                                    $sshOptionsString = implode(' ', $sshOptions);
                                    $ppp = PppoeUser::where('id', $invoice->id_pelanggan);
                                    $ppp->update([
                                        'status' => 1,
                                    ]);
                                    if ($pppoe->nas === null) {
                                        $nas = RadiusNas::where('shortname', $shortname)->select('nasname', 'secret')->get();
                                        foreach ($nas as $row) {
                                            $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                                            $command = "echo $userAttr | radclient -r 1 {$row['nasname']}:3799 disconnect {$row['secret']}";
                                            $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\""; 
                                            $process = Process::run($ssh_command);                                       
                                        }
                                    } else {
                                        $secret = RadiusNas::where('shortname', $shortname)->where('nasname', $pppoe->nas)->select('secret')->first();
                                        $userAttr = escapeshellarg("User-Name = '{$pppoe->username}'");
                                        $command = "echo $userAttr | radclient -r 1 {$pppoe->nas}:3799 disconnect {$secret->secret}";
                                        $ssh_command = "ssh {$sshOptionsString} {$ssh_user}@{$ssh_host} \"{$command}\""; 
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
                    } elseif ($transaction == 'pending') {
                        echo 'Waiting customer to finish transaction order_id: ' . $order_id . ' using ' . $type;
                    } elseif ($transaction == 'deny') {
                        echo 'Payment using ' . $type . ' for transaction order_id: ' . $order_id . ' is denied.';
                    } elseif ($transaction == 'expire') {
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
                            $domain = User::where('shortname',$shortname)->where('role','Admin')->first()->domain;
                            $pppoe = PppoeUser::where('id', $invoice->id_pelanggan)->first();
                            // ganti nomor invoice
                            $no_invoice = date('m') . rand(10000000, 99999999);
                            if ($invoice->status === 'unpaid') {
                                $invoice_update = Invoice::where('no_invoice', $order_id);
                                $invoice_update->update([
                                    'snap_token' => null,
                                    'no_invoice' => $no_invoice,
                                    'payment_url' => $domain . '/pay/' . $no_invoice,
                                ]);

                                try {
                                    $notif_ps = BillingSetting::where('shortname', $shortname)->first()->notif_ps;
                                    if ($pppoe->wa !== null) {
                                        // $message = str_replace($shortcode, $source, $template);
                                        $mpwa = Mpwa::where('shortname', $shortname)->first();
                                        $invoice = Invoice::where('no_invoice', $no_invoice)->first();
                                        $message_format = 'Maaf pembayaran anda kadaluarsa, silakan lakukan pembayaran ulang di link berikut ' . $invoice->payment_url;

                                        try {
                                            $curl = curl_init();
                                            $data = [
                                                'api_key' => $mpwa->api_key,
                                                'sender' => $mpwa->sender,
                                                'number' => $pppoe->wa,
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
                                    return response()->json($cancelResponse);
                                } catch (\Exception $e) {
                                    return response()->json(['error' => $e->getMessage()]);
                                }
                            }
                        }
                        echo 'Sorry your transaction is expired';
                    } elseif ($transaction == 'cancel') {
                        // TODO set payment status in merchant's database to 'Denied'
                        echo 'Payment using ' . $type . ' for transaction order_id: ' . $order_id . ' is canceled.';
                    }
                }
            }
        }
    }
}
