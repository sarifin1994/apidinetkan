<?php

namespace App\Http\Controllers\Hotspot;

use App\Http\Controllers\Controller;
use App\Models\Hotspot\HotspotUser;
use App\Models\Mikrotik\Nas;
use App\Models\Hotspot\HotspotProfile;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Owner\License;
use App\Models\Radius\RadiusNas;
use App\Models\Keuangan\Transaksi;
use App\Models\Radius\RadiusSession;
use Spatie\Activitylog\Contracts\Activity;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HotspotUserImport;
use App\Models\Partnership\Reseller;

class HotspotUserController extends Controller
{
    public function index()
    {
        $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
        $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $totalexpired = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();
        $totalnew = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 1)->count();

        $nas = Nas::where('shortname', multi_auth()->shortname)->select('ip_router', 'name')->get();
        $profiles = HotspotProfile::where('shortname', multi_auth()->shortname)->where('status', 1)->orderBy('name', 'asc')->select('id', 'name', 'price')->get();
        // $remarks = HotspotUser::where('shortname', multi_auth()->shortname)->select('created_at','remark')->distinct()->latest('created_at')->withCount('relatedRemarks')->get();
        $remarks = HotspotUser::where('shortname', multi_auth()->shortname)
    ->selectRaw('remark, created_at, COUNT(*) as remark_count')
    ->groupBy('remark', 'created_at')
    ->orderByDesc('created_at')
    ->get();

        $resellers = Reseller::where('shortname', multi_auth()->shortname)->where('status', 1)->select('id', 'name', 'id_reseller')->get();

        return view('backend.hotspot.user.index_new', compact('profiles', 'nas', 'remarks', 'totaluser', 'totalactive', 'totalexpired', 'totalnew', 'totaldisabled', 'resellers'));
    }

    public function datatable()
    {
        if (request()->ajax()) {
            // Inisialisasi query dasar
            $query = HotspotUser::query()->where('shortname', multi_auth()->shortname)->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'created_by', 'status', 'reseller_id', 'remark','start_time');

            // Pemetaan parameter request ke kolom database
            $filters = [
                'remark' => 'created_at',
                'status' => 'status',
                'nas' => 'nas',
                'reseller' => 'reseller_id',
            ];

            // Tambahkan where secara dinamis jika parameter tidak null
            foreach ($filters as $param => $column) {
                if (request()->get($param) !== null) {
                    $query->where($column, request()->get($param));
                }
            }

            // Cek apakah ada filter yang digunakan
            $hasFilter = request()->get('remark') !== null || request()->get('status') !== null || request()->get('nas') !== null || request()->get('reseller') !== null;

            if ($hasFilter) {
                $datatable = DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($row) {
                        return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row->id . '" value="' . $row->id . '" checked />';
                    })
                    ->rawColumns(['action', 'checkbox'])
                    ->editColumn('nas_name', function ($row) {
                        return $row->radius->name;
                    })
                    ->editColumn('reseller_name', function ($row) {
                        return $row->reseller->name;
                    })
                    ->editColumn('profile_name', function ($row) {
                        return $row->rprofile->name;
                    });
            } else {
                $selectedIds = request()->get('idsel') ?? [];
                $datatable = DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($row) use ($selectedIds) {
                        $checked = in_array($row->id, $selectedIds) ? ' checked' : '';
                        return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row->id . '" value="' . $row->id . '"' . $checked . ' />';
                    })
                    ->rawColumns(['action', 'checkbox'])
                    ->editColumn('reseller_name', function ($row) {
                        return $row->reseller->name;
                    })
                    ->editColumn('nas_name', function ($row) {
                        return $row->radius->name;
                    })
                    ->editColumn('profile_name', function ($row) {
                        return $row->rprofile->name;
                    });
            }

            return $datatable->toJson();
        }
    }

    // public function generate(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'jml_voucher' => 'required',
    //         'profile' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'error' => $validator->errors(),
    //         ]);
    //     }
    //     $jml_voucher = $request->jml_voucher;
    //     if (
    //         (int) HotspotUser::where('shortname', multi_auth()->shortname)->count() + (int) $jml_voucher <
    //         License::where('id', multi_auth()->license_id)
    //             ->select('limit_hs')
    //             ->first()->limit_hs
    //     ) {
    //         $data = [];
    //         for ($x = 0; $x < $jml_voucher; $x++) {
    //             if ($request->character === '1') {
    //                 $characters = '0123456789';
    //             } elseif ($request->character === '2') {
    //                 $characters = 'abcdefghijklmnopqrstuvwxyz';
    //             } elseif ($request->character === '3') {
    //                 $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //             } elseif ($request->character === '4') {
    //                 $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    //             } elseif ($request->character === '5') {
    //                 $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //             }
    //             $length = $request->length;
    //             $charactersLength = strlen($characters);
    //             $user = $request->prefix;
    //             for ($i = 0; $i < $length; $i++) {
    //                 $user .= $characters[rand(0, $charactersLength - 1)];
    //             }
    //             if ($request->model === '1') {
    //                 $pass = $user;
    //             } else {
    //                 $pass = '';
    //                 for ($i = 0; $i < $length; $i++) {
    //                     $pass .= $characters[rand(0, $charactersLength - 1)];
    //                 }
    //                 $pass = $pass;
    //             }
    //             $draw = [
    //                 'shortname' => multi_auth()->shortname,
    //                 'shortname' => multi_auth()->shortname,
    //                 'username' => $user,
    //                 'value' => $pass,
    //                 'profile' => $request->profile,
    //                 'nas' => $request->nas,
    //                 'server' => $request->hotspot_server,
    //                 'status' => 1,
    //                 'reseller_id' => $request->reseller_id,
    //                 'statusPayment' => $request->payment_status,
    //                 'created_at' => Carbon::now(),
    //                 'created_by' => multi_auth()->username,
    //             ];
    //             $data[] = $draw;
    //         }
    //         $insert_data = collect($data);
    //         $inserts = $insert_data->chunk(500);
    //         foreach ($inserts as $key => $insert) {
    //             $inserted = HotspotUser::insert($insert->toArray());
    //         }

    //         if ($request->payment_status === '2' && $request->total !== '0') {
    //             $price = str_replace('.', '', $request->total);
    //             $transaksi = Transaksi::create([
    //                 'shortname' => multi_auth()->shortname,
    //                 'id_data' => 0,
    //                 'tipe' => 'Pemasukan',
    //                 'kategori' => 'Hotspot',
    //                 'nas' => $request->nas,
    //                 'deskripsi' => 'Payment Voucher Generate #' . Carbon::now()->format('d/m/Y H:i:s'),
    //                 'nominal' => $price,
    //                 'tanggal' => Carbon::now(),
    //                 'metode' => 'Cash',
    //                 'created_by' => multi_auth()->username,
    //             ]);
    //             activity()
    //                 ->tap(function (Activity $activity) {
    //                     $activity->shortname = multi_auth()->shortname;
    //                 })
    //                 ->event('Create')
    //                 ->log('Create Transaction Pemasukan: ' . $transaksi->deskripsi . '');
    //         }
    //         // if($request->reseller_id !== null){
    //         // }

    //         // $username = [];
    //         // foreach($data as $index => $row){
    //         //     $username[$index]= $row['username'];
    //         // }
    //         // $selected_id = HotspotUser::whereIn('username',$username)->select('id')->get();

    //         $remark = $data[0]['created_at'];
    //         $selected_id = HotspotUser::where('shortname', multi_auth()->shortname)
    //             ->where('created_at', $remark)
    //             ->select('id')
    //             ->get();
    //         $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
    //         $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)
    //             ->where('status', 2)
    //             ->count();
    //         $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)
    //             ->where('status', 3)
    //             ->count();
    //         $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)
    //             ->where('status', 0)
    //             ->count();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Voucher Berhasil Dibuat',
    //             'id' => $selected_id,
    //             'totaluser' => $totaluser,
    //             'totalactive' => $totalactive,
    //             'totalsuspend' => $totalsuspend,
    //             'totaldisabled' => $totaldisabled,
    //         ]);
    //     } else {
    //         return response()->json([
    //             'error' => 'Sorry your license is limited, please upgrade!',
    //         ]);
    //     }
    // }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jml_voucher' => 'required|integer|min:1',
            'profile' => 'required',
            // Tambahkan validasi tambahan jika diperlukan (character, length, prefix, model, dll)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $jml_voucher = (int) $request->jml_voucher;
        $shortname = multi_auth()->shortname;
        $currentUserCount = HotspotUser::where('shortname', $shortname)->count();
        $licenseLimit = (int) License::where('id', multi_auth()->license_id)->value('limit_hs');

        if ($currentUserCount + $jml_voucher >= $licenseLimit) {
            return response()->json([
                'error' => 'Sorry your license is limited, please upgrade!',
            ]);
        }

        // Gunakan satu timestamp untuk semua voucher
        $now = Carbon::now();

        // Mapping opsi karakter
        $characterOptions = [
            '1' => '0123456789',
            '2' => 'abcdefghijklmnopqrstuvwxyz',
            '3' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '4' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            '5' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ];

        $selectedCharacters = $characterOptions[$request->character] ?? '0123456789';
        $length = (int) $request->length;
        $prefix = $request->prefix;
        $model = $request->model;

        $vouchers = [];
        for ($x = 0; $x < $jml_voucher; $x++) {
            // Buat username dengan prefix dan string acak
            $username = $prefix . $this->generateRandomString($selectedCharacters, $length);
            // Jika model 1, password sama dengan username, selain itu buat password acak
            $password = $model === '1' ? $username : $this->generateRandomString($selectedCharacters, $length);

            $vouchers[] = [
                'shortname' => $shortname,
                'username' => $username,
                'value' => $password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'server' => $request->hotspot_server,
                'status' => 1,
                'reseller_id' => $request->reseller_id,
                'statusPayment' => $request->payment_status,
                'created_at' => $now,
                'created_by' => multi_auth()->username,
                'remark' => $request->remark,
            ];
        }

        // Insert data per chunk agar efisien
        foreach (array_chunk($vouchers, 500) as $chunk) {
            HotspotUser::insert($chunk);
        }

        // Jika status pembayaran '2' dan total tidak nol, buat transaksi
        if ($request->payment_status === '2' && $request->total !== '0') {
            $price = str_replace('.', '', $request->total);
            $transaksi = Transaksi::create([
                'shortname' => $shortname,
                'id_data' => 0,
                'tipe' => 'Pemasukan',
                'kategori' => 'Hotspot',
                'nas' => $request->nas,
                'deskripsi' => 'Payment Voucher Generate #' . $now->format('d/m/Y H:i:s'),
                'nominal' => $price,
                'tanggal' => $now,
                'metode' => 'Cash',
                'created_by' => multi_auth()->username,
            ]);

            activity()
                ->tap(function (Activity $activity) use ($shortname) {
                    $activity->shortname = $shortname;
                })
                ->event('Create')
                ->log('Create Transaction Pemasukan: ' . $transaksi->deskripsi);
        }

        // Ambil ID voucher yang baru saja dibuat berdasarkan timestamp
        $selected_id = HotspotUser::where('shortname', $shortname)->where('created_at', $now)->pluck('id');

        // Hitung total user dan status lainnya
        $totaluser = HotspotUser::where('shortname', $shortname)->count();
        $totalactive = HotspotUser::where('shortname', $shortname)->where('status', 2)->count();
        $totalsuspend = HotspotUser::where('shortname', $shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', $shortname)->where('status', 0)->count();

        return response()->json([
            'success' => true,
            'message' => 'Voucher Berhasil Dibuat',
            'id' => $selected_id,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    /**
     * Fungsi helper untuk menghasilkan string acak
     */
    private function generateRandomString($characters, $length)
    {
        $result = '';
        $maxIndex = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, $maxIndex)];
        }
        return $result;
    }

    public function destroy(Request $request)
    {
        $usernames = HotspotUser::where('shortname', multi_auth()->shortname)->whereIn('id', $request->ids)->select('username')->get();
        $username = $usernames->pluck('username')->toArray();

        $user = HotspotUser::where('shortname', multi_auth()->shortname)->whereIn('id', $request->ids)->delete();
        $session = RadiusSession::where('shortname', multi_auth()->shortname)->whereIn('username', $username)->delete();

        $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
        $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
            'data' => $user,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function show(HotspotUser $user)
    {
        //return response
        $data = HotspotUser::with('session', 'rprofile')->find($user->id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $data,
        ]);
    }

    public function getSession(Request $request)
    {
        $sessions = RadiusSession::where('shortname', multi_auth()->shortname)->where('username', $request->username)->orderBy('id', 'desc')->get();
        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:2', 'max:255', Rule::unique('frradius_auth.user_hs')->where('shortname', multi_auth()->shortname)],
            'password' => ['required', 'string', 'min:2', 'max:255'],
            // 'profile_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        if (HotspotUser::where('shortname', multi_auth()->shortname)->count() < License::where('id', multi_auth()->license_id)->select('limit_hs')->first()->limit_hs) {
            $user = HotspotUser::create([
                'shortname' => multi_auth()->shortname,
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'server' => $request->hotspot_server,
                'status' => 1,
                'reseller_id' => $request->reseller_id,
                'statusPayment' => $request->payment_status,
                'created_by' => multi_auth()->username,
                'remark' => $request->remark,
            ]);
            if ($request->payment_status === '2' && $request->total !== '0') {
                $price = str_replace('.', '', $request->total);
                // if($request->reseller_id !== null){

                // }
                $transaksi = Transaksi::create([
                    'shortname' => multi_auth()->shortname,
                    'id_data' => $user->id,
                    'tipe' => 'Pemasukan',
                    'kategori' => 'Hotspot',
                    'deskripsi' => "Payment Hotspot User #$user->username",
                    'nominal' => $price,
                    'tanggal' => Carbon::now(),
                    'metode' => 'Cash',
                    'created_by' => multi_auth()->username,
                ]);
                activity()
                    ->tap(function (Activity $activity) {
                        $activity->shortname = multi_auth()->shortname;
                    })
                    ->event('Create')
                    ->log('Create Transaction Pemasukan: ' . $transaksi->deskripsi . '');
            }
            $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
            $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
            $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
            $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();
            return response()->json([
                'success' => true,
                'message' => 'User Berhasil Dibuat',
                'data' => $user,
                'totaluser' => $totaluser,
                'totalactive' => $totalactive,
                'totalsuspend' => $totalsuspend,
                'totaldisabled' => $totaldisabled,
            ]);
        } else {
            return response()->json([
                'error' => 'Sorry your license is limited, please upgrade!',
            ]);
        }
    }

    public function update(Request $request, HotspotUser $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:2',
            'password' => 'required|string|min:2',
            'profile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $user->update([
            'username' => $request->username,
            'value' => $request->password,
            'profile' => $request->profile,
            'nas' => $request->nas,
            'server' => $request->hotspot_server,
            'remark' => $request->remark,
        ]);

        // $totaluser = HotspotUser::count();
        // $totalactive = HotspotUser::where('status', 2)->count();
        // $totalsuspend = HotspotUser::where('status', 3)->count();
        // $totaldisabled = HotspotUser::where('status', 0)->count();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
            // 'totaluser' => $totaluser,
            // 'totalactive' => $totalactive,
            // 'totalsuspend' => $totalsuspend,
            // 'totaldisabled' => $totaldisabled,
        ]);
    }

    public function print(Request $request)
    {
        // Pastikan $ids adalah array
        $ids = $request->ids ?? [];

        // Jika array kosong, kembalikan error
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(
                [
                    'error' => 'Tidak ada voucher yang dipilih untuk dicetak.',
                ],
                422,
            );
        }

        $user = HotspotUser::whereIn('id', $ids)->with('rprofile')->orderBy('id', 'desc')->get();

        // Pilih template sesuai parameter
        if ($request->template === 'default') {
            $html = view('backend.hotspot.template.default', compact('user'))->render();
        } elseif ($request->template === 'default_wa') {
            $html = view('backend.hotspot.template.default_wa', compact('user'))->render();
        } else {
            // Jika template tidak sesuai, bisa dikembalikan error atau gunakan template default
            $html = view('backend.hotspot.template.default', compact('user'))->render();
        }

        return response()->json([
            'success' => true,
            'message' => 'Silakan Cetak Voucher Anda',
            'data' => $html,
        ]);
    }

    public function enable(Request $request)
    {
        $user = HotspotUser::whereIn('id', $request->ids);
        $user->update([
            'status' => 1,
        ]);

        $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
        $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function disable(Request $request)
    {
        $user = HotspotUser::whereIn('id', $request->ids);
        $user->update([
            'status' => 0,
        ]);

        $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
        $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function reactivate(Request $request)
    {
        $user = HotspotUser::whereIn('id', $request->ids);
        $user->update([
            'status' => 1,
            'start_time' => null,
            'end_time' => null,
        ]);
        $username = HotspotUser::whereIn('id', $request->ids)->select('username')->first();
        $username = RadiusSession::where('shortname', multi_auth()->shortname)->whereIn('username', $username)->delete();

        $totaluser = HotspotUser::where('shortname', multi_auth()->shortname)->count();
        $totalactive = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 2)->count();
        $totalsuspend = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 3)->count();
        $totaldisabled = HotspotUser::where('shortname', multi_auth()->shortname)->where('status', 0)->count();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Direactivate',
            'data' => $user,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function online()
    {
        if (request()->ajax()) {
            $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', multi_auth()->shortname)->groupBy('username');
            $online = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
                ->joinSub($sub, 'latest', function ($join) {
                    $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
                })
                ->where([
                    ['user_session.shortname', '=', multi_auth()->shortname],
                    ['user_session.status', '=', 1],
                    ['user_session.type', '=', 1],
                    ['user_session.stop', '=', null], // hanya yang belum stop
                ])
                ->with('mnas', 'ppp:username,full_name,kode_area,kode_odp')
                ->get();

            return DataTables::of($online)->addIndexColumn()->toJson();
        }
        return view('backend.hotspot.online.index_new');
    }

    public function kick(Request $request)
    {
        $ssh_user = env('IP_RADIUS_USERNAME');
        $ssh_host = env('IP_RADIUS_SERVER');
        $user = HotspotUser::where('shortname', multi_auth()->shortname)->where('username', $request->username)->select('username', 'nas')->first();
        if ($user->nas === null) {
            $nas = RadiusNas::where('shortname', multi_auth()->shortname)->select('nasname', 'secret')->get();
            foreach ($nas as $item) {
                $command = "echo User-Name='$user->username' | radclient -r 1 $item[nasname]:3799 disconnect $item[secret]";
                $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
                $process = Process::run($ssh_command);
                
                
            }
        } else {
            $secret = RadiusNas::where('nasname', $user->nas)->select('secret')->first();
            $command = "echo User-Name='$user->username' | radclient -r 1 $user->nas:3799 disconnect $secret->secret";
            $ssh_command = "ssh {$ssh_user}@{$ssh_host} \"{$command}\"";
            $process = Process::run($ssh_command);
            
            
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dikick',
        ]);
    }

    public function import(Request $request)
    {
        // Validasi file import (sesuaikan ekstensi file yang diperbolehkan)
        $request->validate([
            'select_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new HotspotUserImport(), $request->file('select_file'));
            return redirect()->back()->with('success', 'Import user berhasil.');
        } catch (\Exception $e) {
            // Untuk debugging, Anda bisa menyimpan pesan error ke session dan menampilkannya di view
            return redirect()
                ->back()
                ->with('error', 'Import user gagal: ' . $e->getMessage());
        }
    }
}
