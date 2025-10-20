<?php


namespace App\Http\Controllers\Api\Sales;



use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\BankAccount;
use App\Models\Partnership\Mitra;
use App\Models\Setting\Mduitku;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class WidrawalController extends Controller
{
    public function index(Request $request)
    {
        $duitku = Mduitku::query()->where('shortname', $request->user()->shortname)->first();
        $listBank = bank_list();
        $bank = BankAccount::query()->where('id_mitra', $request->user()->id_mitra)->first();
//        return view('backend.widrawal.index',compact('listBank', 'bank', 'duitku'));
//        return response()->json($request->user());
        return response()->json([
            'listBank' => $listBank,
            'bank' => $bank,
            'widrawal' => [
                'status_widrawal' => $duitku->status_widrawal,
                'fee_disburs' => $duitku->fee_disburs,
                'minimal_disburs' => $duitku->minimal_disburs
            ]
        ]);
    }

    public function inq_account(Request $request){
        try{
            $duitku = Mduitku::query()->where('shortname', $request->user()->shortname)->first();
            $bank = BankAccount::query()->where('id_mitra', $request->user()->id_mitra)->first();
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
                    if($bank){
                        $bank->update([
                            'bank_name' => bank_list($bankCode),
                            'bank_code' => $bankCode,
                            'account_name' => $response->accountName,
                            'account_number' => $bankAccount
                        ]);
                    }else{
                        BankAccount::create([
                            'id_mitra' => $request->user()->id_mitra,
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
        $duitku = Mduitku::query()->where('shortname', $request->user()->shortname)->first();
        if($request->nominal < $duitku->minimal_disburs){
            return response()->json([
                'error' => 'Exception occurred',
                'message' => 'Minimal Penarikan Rp '. number_format($duitku->minimal_disburs, 0, ',', '.')
            ], 500);
        }
        $rekening = BankAccount::query()->where('id_mitra', $request->user()->id_mitra)->first();
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
        $duitku = Mduitku::query()->where('shortname', $request->user()->shortname)->first();
        $rekening = BankAccount::query()->where('id_mitra', $request->user()->id_mitra)->first();
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
            $mitra = Mitra::query()->where('id_mitra', $request->user()->id_mitra)->first();
            $mitra->update([
                'balance' => ($mitra->balance - $amountTransfer + $duitku->fee_disburs)
            ]);

            $history = Balancehistory::create([
                'id_mitra' => $request->user()->id,
                'id_transaksi' => 0,
                'id_reseller' => 0,
                'tx_amount' => ($amountTransfer + $duitku->fee_disburs),
                'notes' => 'penarikan dana sebesar Rp '.number_format($amountTransfer, 0, ',', '.')
                    .', Admin : Rp '.number_format($duitku->fee_disburs, 0, ',', '.')
                    .' Ditarik Ke : '.$rekening->bank_name .' a/n '. $rekening->account_name .' - '. $rekening->account_number,
                'type' => 'out',
                'tx_date' => Carbon::now(),
                'is_widraw' => 1,
                'last_balance' => ($mitra->balance)
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
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = Balancehistory::query()->where('id_mitra', $request->user()->id);
        // 🔍 FILTER OPSIONAL
        if ($request->filled('notes')) {
            $query->where('notes', 'like', '%' . $request->notes . '%');
        }
        $transaksi = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($transaksi);
    }
}

