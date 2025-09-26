<?php


namespace App\Http\Controllers\Widrawal;


use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\BankAccount;
use App\Models\Keuangan\Transaksi;
use App\Models\Partnership\Mitra;
use App\Models\Setting\Mduitku;
use App\Models\Setting\MduitkuOwner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
use Yajra\DataTables\Facades\DataTables;

class WidrawalController extends Controller
{
    public function index()
    {
        $duitku = Mduitku::query()->where('shortname', multi_auth()->shortname)->first();
        $listBank = bank_list();
        $bank = BankAccount::query()->where('id_mitra', multi_auth()->id_mitra)->first();
        if (request()->ajax()) {
            $balancehistory = Balancehistory::query()->where('id_mitra', multi_auth()->id)->get();
            return DataTables::of($balancehistory)
                ->addIndexColumn()

                ->toJson();;
        }
        return view('backend.widrawal.index',compact('listBank', 'bank', 'duitku'));
    }

    public function inq_account(Request $request){
        $duitku = Mduitku::query()->where('shortname', multi_auth()->shortname)->first();
        try{
            $url = '';

            if(env('APP_ENV') == 'development'){
                $url = 'https://sandbox.duitku.com/webapi/api/disbursement/inquirysandbox';
            }
            if(env('APP_ENV') == 'production'){
                $url = 'https://passport.duitku.com/webapi/api/disbursement/inquiry';
            }
            $userId = $duitku->user_id;
            $secretKey = $duitku->secret_key;
            $amountTransfer = 50000;
            $bankAccount = trim($request->account_number);
            $bankCode = $request->bank;
            $email = $duitku->email_disburs;
            $purpose = 'Radiusqu Disbursement';
            $timestamp = round(microtime(true) * 1000);
//            $senderId = "";
//            $senderName = 'Radiusqu';
            $paramSignature = $email . $timestamp . $bankCode . $bankAccount . $amountTransfer . $purpose . $secretKey;

            $signature = hash('sha256', $paramSignature);

            $params = array(
                'userId' => $userId,
                'amountTransfer' => $amountTransfer,
                'bankAccount' => $bankAccount,
                'bankCode' => $bankCode,
                'email' => $email,
                'purpose' => $purpose,
                'timestamp' => $timestamp,
//                'senderId' => $senderId,
//                'senderName' => $senderName,
                'signature' => $signature
            );

            $params_string = json_encode($params);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($params_string))
            );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            //execute post
            $request = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                $response = json_decode($request);
                if($response->responseCode == 00){
                    $bank = BankAccount::query()->where('id_mitra', multi_auth()->id_mitra)->first();
                    if($bank){
                        $bank->update([
                            'bank_name' => bank_list($bankCode),
                            'bank_code' => $bankCode,
                            'account_name' => $response->accountName,
                            'account_number' => $bankAccount
                        ]);
                    }else{
                        BankAccount::create([
                            'id_mitra' => multi_auth()->id_mitra,
                            'bank_name' => bank_list($bankCode),
                            'bank_code' => $bankCode,
                            'account_name' => $response->accountName,
                            'account_number' => $bankAccount
                        ]);
                    }
                    return response()->json($response);
                }else{
                    return response()->json([
                        'error' => true,
                        'message' => $response->responseDesc
                    ], 500);
                }
            } else {
                echo $httpCode;
            }

        }catch (\Exception $ex){

        }
    }

    function inquiry(Request $request){
        $duitku = Mduitku::query()->where('shortname', multi_auth()->shortname)->first();
        if($request->nominal < $duitku->minimal_disburs){
            return response()->json([
                'error' => 'Exception occurred',
                'message' => 'Minimal Penarikan Rp '. number_format($duitku->minimal_disburs, 0, ',', '.')
            ], 500);
        }
        $rekening = BankAccount::query()->where('id_mitra', multi_auth()->id_mitra)->first();
        $url = '';
        $userId         = $duitku->user_id;
        $secretKey      = $duitku->secret_key;
        $amountTransfer = $request->nominal;
        $bankAccount    = $rekening->account_number;
        $bankCode       = $rekening->bank_code;
        $email          = $duitku->email_disburs;
        $purpose = 'Radiusqu Disbursement';
        $timestamp      = round(microtime(true) * 1000);
        $paramSignature = $email . $timestamp . $bankCode . $bankAccount . $amountTransfer . $purpose . $secretKey;

        $signature = hash('sha256', $paramSignature);

        $params = array(
            'userId'         => $userId,
            'amountTransfer' => $amountTransfer,
            'bankAccount'    => $bankAccount,
            'bankCode'       => $bankCode,
            'email'          => $email,
            'purpose'        => $purpose,
            'timestamp'      => $timestamp,
            'signature'      => $signature
        );

        if(env('APP_ENV') == 'development'){
            $url = 'https://sandbox.duitku.com/webapi/api/disbursement/inquirysandbox';
        }
        if(env('APP_ENV') == 'production'){
            $url = 'https://passport.duitku.com/webapi/api/disbursement/inquiry';
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $params);

            if ($response->successful()) {
                $result = $response->json();
                // Redirect to paymentUrl or return view
                return $result;
            } else {
                return response()->json([
                    'error' => 'Request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function payment(Request $request){
        $duitku = Mduitku::query()->where('shortname', multi_auth()->shortname)->first();
        $rekening = BankAccount::query()->where('id_mitra', multi_auth()->id_mitra)->first();
        $url = '';

        $disburseId     = $request->disburseId;
        $userId         = $duitku->user_id;
        $secretKey      = $duitku->secret_key;
        $amountTransfer = $request->nominal;
        $bankAccount    = $rekening->account_number;
        $bankCode       = $rekening->bank_code;
        $email          = $duitku->email_disburs;
        $purpose        = 'Radiusqu Disbursement';
        $timestamp      = round(microtime(true) * 1000);
        $accountName    = $rekening->account_name;
        $custRefNumber  = $request->custRefNumber;
        $paramSignature = $email . $timestamp . $bankCode . $bankAccount . $accountName . $custRefNumber . $amountTransfer . $purpose . $disburseId . $secretKey;

        $signature = hash('sha256', $paramSignature);

        $params = array(
            'disburseId'     => $disburseId,
            'userId'         => $userId,
            'email'          => $email,
            'bankCode'       => $bankCode,
            'bankAccount'    => $bankAccount,
            'amountTransfer' => $amountTransfer,
            'accountName'    => $accountName,
            'custRefNumber'  => $custRefNumber,
            'purpose'        => $purpose,
            'timestamp'      => $timestamp,
            'signature'      => $signature
        );
        if(env('APP_ENV') == 'development'){
            $url = 'https://sandbox.duitku.com/webapi/api/disbursement/transfersandbox';
        }
        if(env('APP_ENV') == 'production'){
            $url = 'https://passport.duitku.com/webapi/api/disbursement/transfer';
        }
        try {
            DB::beginTransaction();

            // potong saldo
            $mitra = Mitra::query()->where('id_mitra', multi_auth()->id_mitra)->first();
            $mitra->update([
                'balance' => ($mitra->balance - $amountTransfer + $duitku->fee_disburs)
            ]);

            $history = Balancehistory::create([
                'id_mitra' => multi_auth()->id,
                'id_transaksi' => 0,
                'id_reseller' => 0,
                'tx_amount' => ($amountTransfer + $duitku->fee_disburs),
                'notes' => 'penarikan dana sebesar Rp '.number_format($amountTransfer, 0, ',', '.')
                            .', Admin : Rp '.number_format($duitku->fee_disburs, 0, ',', '.')
                            .' Ditarik Ke : '.$rekening->bank_name .' a/n '. $rekening->account_name .' - '. $rekening->account_number,
                'type' => 'out',
                'tx_date' => Carbon::now(),
                'is_widraw' => 1
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $params);

            if ($response->successful()) {
                $result = $response->json();
                // Redirect to paymentUrl or return view
                DB::commit();
                return $result;
            } else {
                DB::rollBack();
                return response()->json([
                    'error' => 'Request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Exception occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request){
        $duitku = Mduitku::where('shortname', multi_auth()->shortname)->first();
        if (request()->ajax()) {
//            $type = request()->get('type');
//            if($type == 'all'){
//                $transaksi = Transaksi::query()->where('shortname', multi_auth()->shortname)->where('created_by','duitku');
//            }
//            if($type == 'harian'){
//                // 1. Data Harian (Hari ini)
//                $transaksi = Transaksi::query()
//                    ->where('shortname', multi_auth()->shortname)
//                    ->where('created_by', 'duitku')
//                    ->whereDate('created_at', Carbon::today())
//                    ->get();
//            }
//            if($type == 'mingguan'){
//                // 2. Data Mingguan (Minggu ini, mulai Senin)
//                $transaksi = Transaksi::query()
//                    ->where('shortname', multi_auth()->shortname)
//                    ->where('created_by', 'duitku')
//                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
//                    ->get();
//            }
//            if($type == 'bulanan'){
//                // 3. Data Bulanan (Bulan ini)
//                $transaksi = Transaksi::query()
//                    ->where('shortname', multi_auth()->shortname)
//                    ->where('created_by', 'duitku')
//                    ->whereMonth('created_at', Carbon::now()->month)
//                    ->whereYear('created_at', Carbon::now()->year)
//                    ->get();
//            }
//            if($type == 'tahunan'){
//                $transaksi = Transaksi::query()
//                    ->where('shortname', multi_auth()->shortname)
//                    ->where('created_by', 'duitku')
//                    ->whereYear('created_at', Carbon::now()->year)
//                    ->get();
//            }
            $transaksi = Balancehistory::query()->where('id_mitra', multi_auth()->id);
            return DataTables::of($transaksi)
                ->addIndexColumn()
                ->toJson();
        }
        return view('backend.widrawal.history');
    }
}
