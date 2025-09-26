<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting\MduitkuOwner;
use Illuminate\Http\Request;
use App\Models\Owner\License;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Carbon\Carbon;
use App\Models\User;

class LicenseController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $license = License::query();
            return DataTables::of($license)
                ->addIndexColumn()
                ->addColumn('limit_hs', function ($row) {
                    return number_format($row->limit_hs, 0, ',', '.') . ' User'; // Format ribuan
                })
                ->addColumn('limit_pppoe', function ($row) {
                    return number_format($row->limit_pppoe, 0, ',', '.') . ' User'; // Format ribuan
                })
                ->addColumn('custome', function ($row) {
                    if ($row->custome == 0) {
                        return 'NO';
                    }
                    if ($row->custome == 1) {
                        return 'YES';
                    }
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '
                        <a href="javascript:void(0)" id="edit"
                        data-id="' . $row->id . '"
                        class="btn btn-secondary text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <i class="ti ti-edit"></i>
                        </a>';

                    if ($row->status === 1) {
                        $toggleBtn = '
                            <a href="javascript:void(0)" id="disable" data-id="' . $row->id . '"
                            class="btn btn-primary text-white"
                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-user-off"></i>
                            </a>';
                    } else {
                        $toggleBtn = '
                            <a href="javascript:void(0)" id="enable" data-id="' . $row->id . '"
                            class="btn btn-primary text-white"
                            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-user-check"></i>
                            </a>';
                    }

                    return $editBtn . $toggleBtn;
                })
                ->toJson();
        }
        return view('backend.license.owner.index_new');
    }

    public function store(Request $request)
    {
        // if ($request->price !== null) {
        //     $price = str_replace('.', '', $request->price);
        // } else {
        //     $price = 0;
        // }
        $license = License::create([
            'name' => $request->name,
            'deskripsi' => $request->deskripsi,
            'spek' => $request->spek,
            'price' => $request->price,
            'limit_hs' => $request->limit_hs,
            'limit_pppoe' => $request->limit_pppoe,
            'midtrans' => $request->midtrans,
            'olt' => $request->olt,
            'custome' => $request->custome
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $license,
        ]);
    }

    public function show(License $license)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $license,
        ]);
    }

    public function update(Request $request, License $license)
    {

        $license->update([
            'name' => $request->name,
            'deskripsi' => $request->deskripsi,
            'spek' => $request->spek,
            'price' => $request->price,
            'limit_hs' => $request->limit_hs,
            'limit_pppoe' => $request->limit_pppoe,
            'midtrans' => $request->midtrans,
            'olt' => $request->olt,
            'custome' => $request->custome
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $license,
        ]);
    }

    public function destroy($id)
    {
        $area = Pop::findOrFail($id);
        $area->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $license = License::where('id', $request->id);
        $license->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'License Berhasil Dinonaktifkan',
            'data' => $license,
        ]);
    }

    public function enable(Request $request)
    {
        $license = License::where('id', $request->id);
        $license->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'License Berhasil Diaktifkan',
            'data' => $license,
        ]);
    }

    public function license()
    {
        $licenses = License::where('status', 1)->where('id', '!=', 1)->where('custome', 0)->get();
        return view('backend.license.index_new', compact('licenses'));
    }
    public function orderLicense(Request $request)
    {
        $license = License::findOrFail($request->id);

        $prefix = 'RQ-' . date('m'); // Format BulanTanggal (Misal: 0310 untuk 10 Maret)
        $randomNumber = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); // 6 karakter acak
        $order_number = $prefix . '' . $randomNumber;

        $user = User::where('username', multi_auth()->username)->first();

        if ($user && isset($user->discount) && $user->discount > 0) {
            // Menghitung potongan harga
            $discountAmount = $user->discount;
            // Harga setelah diskon
            $price = $license->price - $discountAmount;
        } else {
            // Jika tidak ada diskon
            $discountAmount = 0;
            $price = $license->price;
        }

        // Data untuk transaksi Midtrans
        //        $transactionDetails = [
        //            'transaction_details' => [
        //                'order_id' => $order_number,
        //                'gross_amount' => $price,
        //                'discount' => $discountAmount,
        //            ],
        //            'customer_details' => [
        //                'first_name' => multi_auth()->name,
        //                'email' => multi_auth()->email,
        //                'phone' => multi_auth()->whatsapp,
        //            ],
        //            'item_details' => [
        //                [
        //                    'id' => 'a1l',
        //                    'price' => $license->price,
        //                    'quantity' => 1,
        //                    'name' => 'x Lisensi Radiusqu ' . $license->name,
        //                ],
        //                [
        //                    'id' => 'discount',
        //                    'price' => (int)-$discountAmount,
        //                    'quantity' => 1,
        //                    'name' => 'Discount ' . $discountAmount,
        //                ],
        //            ],
        //        ];

        $itemDetails = [
            [
                'id' => 'a1l',
                'price' => $license->price,
                'quantity' => 1,
                'name' => 'x Lisensi Radiusqu ' . $license->name,
            ],
            [
                'id' => 'discount',
                'price' => (int)-$discountAmount,
                'quantity' => 1,
                'name' => 'Discount ' . $discountAmount,
            ],
        ];



        $transaction = [
            'merchantOrderId' => $order_number,
            'paymentAmount'   => (int) round($price),
            'productDetails'  => $license->name,
            'additionalParam' => '',
            'merchantUserInfo' => '',
            'customerVaName'  => multi_auth()->name,
            'email'           => multi_auth()->email,
            'phoneNumber'     => multi_auth()->whatsapp,
            'itemsDetails'    => $itemDetails,
            'customerDetails' => [
                'firstName'   => multi_auth()->name,
                'email'       => multi_auth()->email,
                'phoneNumber' => multi_auth()->whatsapp,
            ],
            'callbackUrl'     => route('owner_duitku.callback'),
            'returnUrl'       => route('order.confirm', $order_number),
            'expiryPeriod'    => 60 * 24, // 1440 minutes
        ];

        // Decide sandbox vs production
        $duitku = MduitkuOwner::get()->first();
        $url = $duitku->environment == "production"
            ? $duitku->url_production
            : $duitku->url_development;

        // Use millisecond timestamp to avoid "Request Expired"
        $timestamp = (int) round(microtime(true) * 1000);

        // Generate signature with HMAC-SHA256
        $merchantCode = $duitku->id_merchant;
        $apiKey       = $duitku->api_key;

        $signature = hash_hmac('sha256', $merchantCode . $timestamp, $apiKey);

        // Send the POST request
        $response = Http::withHeaders([
            'Accept'               => 'application/json',
            'Content-Type'         => 'application/json',
            'x-duitku-signature'   => $signature,
            'x-duitku-timestamp'   => $timestamp,
            'x-duitku-merchantcode' => $merchantCode,
        ])->post($url, $transaction);

        // Generate Snap Token
        //        $snapToken = Snap::getSnapToken($transactionDetails);

        $user = User::where('username', multi_auth()->username);
        $user->update([
            'order' => $license->id,
            'order_number' => $order_number,
            'order_status' => 'unpaid',
        ]);

        //        return response()->json([
        //            'snap_token' => "",
        //            'order_id' => $order_number, // Kirim order_number ke frontend
        //        ]);

        return response()->json(
            [
                'paymentUrl' => isset($response['paymentUrl']) ? $response['paymentUrl'] : '',
                'order_id' => $order_number, // Kirim order_number ke frontend
            ]
        );
    }

    public function orderLicense_midtrans(Request $request)
    {
        $license = License::findOrFail($request->id);

        $server_key = env('SERVER_MIDTRANS');
        $client_key = env('CLIENT_MIDTRANS');
        $midtrans_status = env('STATUS_MIDTRANS');

        Config::$serverKey = $server_key;
        $client_key = Config::$clientKey = $client_key;
        if ($midtrans_status === 'Production') {
            Config::$isProduction = true;
        } else {
            Config::$isProduction = false;
        }
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $prefix = 'RQ-' . date('m'); // Format BulanTanggal (Misal: 0310 untuk 10 Maret)
        $randomNumber = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); // 6 karakter acak
        $order_number = $prefix . '' . $randomNumber;

        $user = User::where('username', multi_auth()->username)->first();

        if ($user && isset($user->discount) && $user->discount > 0) {
            // Menghitung potongan harga
            $discountAmount = $user->discount;
            // Harga setelah diskon
            $price = $license->price - $discountAmount;
        } else {
            // Jika tidak ada diskon
            $discountAmount = 0;
            $price = $license->price;
        }

        // Data untuk transaksi Midtrans
        $transactionDetails = [
            'transaction_details' => [
                'order_id' => $order_number,
                'gross_amount' => $price,
                'discount' => $discountAmount,
            ],
            'customer_details' => [
                'first_name' => multi_auth()->name,
                'email' => multi_auth()->email,
                'phone' => multi_auth()->whatsapp,
            ],
            'item_details' => [
                [
                    'id' => 'a1',
                    'price' => $license->price,
                    'quantity' => 1,
                    'name' => 'x Lisensi Radiusqu ' . $license->name,
                ],
                [
                    'id' => 'discount',
                    'price' => (int)-$discountAmount,
                    'quantity' => 1,
                    'name' => 'Discount ' . $discountAmount,
                ],
            ],
        ];

        // Generate Snap Token
        $snapToken = Snap::getSnapToken($transactionDetails);

        $user = User::where('username', multi_auth()->username);
        $user->update([
            'order' => $license->id,
            'order_number' => $order_number,
            'order_status' => 'unpaid',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $order_number, // Kirim order_number ke frontend
        ]);
    }
    public function status($order_number)
    {
        $user = User::where('username', multi_auth()->username)->where('order_number', $order_number)->with('license')->first();
        if (!$user) {
            return abort(404, 'Order id tidak ditemukan');
        }
        return view('backend.license.status', compact('user'));
    }
}
