<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\DataTables\Admin\MemberDinetkanDataTable;
use App\Models\BillingService;
use App\Models\BillingServiceItem;
use App\Models\CountNumbering;
use App\Models\MemberDinetkan;
use App\Models\ProductDInetkan;
use App\Models\Province;
use App\Models\Setting\Mduitku;
use App\Models\User;
use App\Models\UserDinetkan;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MemberDinetkanController
{

    public function index(Request $request)
    {

        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $query = MemberDinetkan::where('dinetkan_user_id', $request->user()->dinetkan_user_id)->with('service');
        // 🔍 FILTER OPSIONAL
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }

        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('wa')) {
            $query->where('wa', 'like', '%' . $request->wa . '%');
        }

        $memberdinetkan = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($memberdinetkan);
    }

    protected function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function generateMemberId(): string
    {
        // Get the latest member ID or start from 0
        $lastMember = CountNumbering::where('tipe', 'member_dinetkan')->first();
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros

        $lastMember->count = $nextNumber;
        $lastMember->save();
        $userid = Carbon::now()->format('Ym').str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $userid;
    }

    public function store(Request $request)
    {
        try {
            $user = MemberDinetkan::create([
                'group_id' => $request->user()->id,
                'id_member' => $this->generateMemberId(),
                'full_name' =>  $request->first_name." ". $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' =>  $request->email,
                'wa' =>  $request->wa,
                'address' =>  $request->address,
                'no_ktp' =>  $request->no_ktp,
                'npwp' =>  $request->npwp,
                'dinetkan_user_id' => $request->user()->dinetkan_user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
            ]);

            $product = ProductDInetkan::where('id', $request->product_dinetkan_id)->first();
            if($product){
                $data = [
                    'product_dinetkan_id' =>  $request->product_dinetkan_id,
                    'product_name' =>  $product->product_name,
                    'product_price' =>  $product->price,
                    'product_ppn' =>  $product->ppn,
                    'product_bhp' =>  $product->bhp,
                    'product_uso' =>  $product->uso,
                ];

                // Only update password if a new one is provided
                $user->update($data);

            }

            return response()->json($user);
        }  catch (UniqueConstraintViolationException $e) {
            $fullMessage = $e->getMessage();

            // Ambil hanya bagian error sebelum `(Connection:` jika ada
            if (preg_match('/^(SQLSTATE\[23000\].*?)( \(Connection:.*)?$/', $fullMessage, $matches)) {
                $cleanMessage = $matches[1]; // hanya bagian SQLSTATE... Duplicate entry ...
            } else {
                $cleanMessage = $fullMessage; // fallback kalau regex gagal
            }

            return response()->json([
                'message' => 'Data sudah ada (duplikat): ' . $cleanMessage
            ], 409);
        } catch (QueryException $e) {
            // Tangani kesalahan query umum (misal kolom tidak ditemukan, syntax error)
            return response()->json([
                'message' => 'Kesalahan Query Database: ' . $e->getMessage()
            ], 500);
        } catch (PDOException $e) {
            // Tangani error dari PDO (biasanya koneksi DB atau masalah level rendah)
            return response()->json([
                'message' => 'Kesalahan PDO: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            // Tangani semua error tak terduga lainnya
            return response()->json([
                'message' => 'Kesalahan Umum: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(UserDinetkan $user)
    {
        $user->company_name = $user->company->name;
        return response()->json($user);
    }

    public function update(Request $request)
    {
        try {
            $memberDinetkan = MemberDinetkan::where('id', $request->id)->first();
            $data = [
                'full_name' =>  $request->first_name." ". $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' =>  $request->email,
                'wa' =>  $request->wa,
                'address' =>  $request->address,
                'no_ktp' =>  $request->no_ktp,
                'npwp' =>  $request->npwp,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
            ];

            // Only update password if a new one is provided

            $memberDinetkan->update($data);

            $product = ProductDInetkan::where('id', $request->product_dinetkan_id)->first();
            if($product){
                $data = [
                    'product_dinetkan_id' =>  $request->product_dinetkan_id,
                    'product_name' =>  $product->product_name,
                    'product_price' =>  $product->price,
                    'product_ppn' =>  $product->ppn,
                    'product_bhp' =>  $product->bhp,
                    'product_uso' =>  $product->uso,
                ];

                // Only update password if a new one is provided
                $memberDinetkan->update($data);

            }

            return response()->json($memberDinetkan);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating admin: ' . $e->getMessage()], 500);
        }
    }



    public function update_product(Request $request)
    {
        try {
            $memberDinetkan = MemberDinetkan::where('id', $request->id)->first();
            $product = ProductDInetkan::where('id', $request->product_dinetkan_id)->first();
            if($product){
                $data = [
                    'product_dinetkan_id' =>  $request->product_dinetkan_id,
                    'product_name' =>  $product->product_name,
                    'product_price' =>  $product->price,
                    'product_ppn' =>  $product->ppn,
                    'product_bhp' =>  $product->bhp,
                    'product_uso' =>  $product->uso,
                ];

                // Only update password if a new one is provided
                $memberDinetkan->update($data);

            }
            return redirect()->back()->with('success', 'Data Updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting admin: ' . $e->getMessage());
        }
    }
    public function destroy(UserDinetkan $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'Admin deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting admin: ' . $e->getMessage());
        }
    }

    public function status(UserDinetkan $user)
    {
        $user->status = $user->status === UserStatusEnum::ACTIVE ? UserStatusEnum::INACTIVE : UserStatusEnum::ACTIVE;
        $user->save();

        return response()->json(['message' => 'User status has been updated']);
    }

    public function single($id){
        $member = MemberDinetkan::where('id', $id)->first();
        return response()->json($member);
    }

    public function mapping_service_pay($id){
        $billing = BillingService::where('id', $id)->first();
        $total = $billing->total_ppn + $billing->total_bhp + $billing->total_uso;
        $paymentMethod = $this->get_payment_method();
        $item=[];
        $item[]=[
            'billing_id' => $billing->id,
            'deskripsi' => "Total Pembayaran $billing->month $billing->year",
            'total' => $total,
            'bank_name' => $billing->bank_name,
            'virtual_account' => $billing->virtual_account,
            'status' => $billing->status,
            'detail' => [
                [
                    'deskripsi' => "Total PPN",
                    'total' => $billing->total_ppn,
                ],
                [
                    'deskripsi' => "Total BHP",
                    'total' => $billing->total_bhp,
                ],
                [
                    'deskripsi' => "Total USO",
                    'total' => $billing->total_uso,
                ]
            ]
        ];
        return response()->json($item);
    }

    protected function get_payment_method(){
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
        if(env('APP_ENV')=='production'){
            $url = 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        }
        $response = makeRequest($url, "POST", $params);
        $data = $response;
        $paymentMethod = [];
        $paymentMethod[]='Select Payment';
        if (isset($data['paymentFee'])) {
            $filteredVA = collect($data['paymentFee'])->filter(function ($item) {
                return Str::contains($item['paymentName'], ' VA');
            });

            // Contoh: ambil hanya paymentName-nya
            $paymentNames = $filteredVA->pluck('paymentName', 'paymentMethod');

            // Tampilkan
            foreach ($paymentNames as $key=>$val) {
                $paymentMethod[$key] = $val;
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }

    public function mapping_service_unpaid(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $mapping = BillingService::where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status','unpaid')
            ->paginate($perPage);
        return response()->json($mapping);
    }

    public function mapping_service_paid(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $mapping = BillingService::where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status','paid')
            ->paginate($perPage);
        return response()->json($mapping);
    }

    public function mapping_service_by_id($id){
        $mapping = BillingService::where('dinetkan_user_id', $request->user()->dinetkan_user_id)->where('id', $id)->first();
        return response()->json($mapping);
    }

    public function update_mapping_service(Request $request){
        $billing = BillingService::where('id', $request->setoran_id)->first();
        $data = [
            'status' => 'paid',
            'paid_via' => $request->paid_via,
            'paid_date' => \Carbon\Carbon::now()->format('Y-m-d H:i')
        ];

        // Only update password if a new one is provided
        $billing->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Setoran Berhasil Dibayar',
        ]);
    }

    public function generate_va(Request $request){
        $billingservice = BillingService::where('id', $request->billing_id)->first();
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        $user = User::where('dinetkan_user_id', $billingservice->dinetkan_user_id)->first();
        // true for sandbox mode
        $url = "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry";

        if(env('APP_ENV') == 'production'){
            $url = "https://passport.duitku.com/webapi/api/merchant/v2/inquiry";
        }

        $ppn = $billingservice->total_ppn;
        $bhp = $billingservice->total_bhp;
        $uso = $billingservice->total_uso;
        $totalItemPrice = $ppn + $bhp + $uso;

        $merchantCode = $duitku->id_merchant;
        $paymentAmount = $totalItemPrice;
        $paymentMethod = $request->payment_method; // PaymentMethod list => https://docs.duitku.com/pop/id/#payment-method
        $phoneNumber = $user->whatsapp; // your customer phone number (optional)
        $productDetails = "Periode : ".$billingservice->month." ".$billingservice->year;
        $merchantOrderId = "#setoran".$billingservice->id;
        $customerVaName = $user->name; // display name on bank confirmation display
        $callbackUrl = env('APP_URL')."duitku/notification_setoran";
        $returnUrl = "";
        $expiryPeriod = 60; // set the expired time in minutes
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $duitku->api_key);

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

        $params = [
            'merchantcode' => $merchantCode,
            'paymentMethod' => $paymentMethod,
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
            'signature' => $signature
        ];

        $response = makeRequest($url, "POST", $params);
        if(isset($response['statusCode'])){
            if($response['statusCode'] == '00' && $response['vaNumber'] != ''){
                $data_update=[
                    'virtual_account' => $response['vaNumber'],
                    'bank' => $request->payment_method,
                    'bank_name' => $request->bank_name,
                    'reference' => $response['reference']
                ];
                $billingservice->update($data_update);
            }
            return [
                'vaNumber' => $response['vaNumber'],
                'bank_name' => $request->bank_name
            ];
        }
        return $response;
    }

    public function mapping_service_item(Request $request, $month, $year)
    {
        $perPage = $request->get('per_page', 10); // default 10 item per halaman
        $mapping = BillingServiceItem::where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('month',$month)
            ->where('year',$year)
            ->paginate($perPage);
        return response()->json($mapping);
    }

    public function single_delete($id){
        $member = MemberDinetkan::where('id', $id)->first();
        $member->delete();
        return response()->json(['message' => 'Data Deleted successfully'], 200);
    }

    public function edit_pelanggan(Request $request, $id){
        $member = MemberDinetkan::where('id', $id)->with(['service','province','regency','district','village'])->first();
        return response()->json($member);
    }
}
