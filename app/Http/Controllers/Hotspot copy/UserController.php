<?php

namespace App\Http\Controllers\Hotspot;

use App\Models\Nas;
use App\Models\Invoice;
use App\Models\License;
use App\Models\Transaksi;
use App\Models\HotspotUser;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use App\Models\HotspotProfile;
use Illuminate\Support\Carbon;
use App\Models\HotspotReseller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Enums\TransactionCategoryEnum;
use Spatie\Activitylog\Models\Activity;
use App\Models\HotspotTransaksiReseller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\DataTables\Admin\Hotspot\UserDataTable;
use App\Http\Requests\Admin\HotspotUserRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $totalNew = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalActive = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalExpired = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();
        $totalDisabled = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();

        $nas = Nas::where('group_id', $request->user()->id_group)
            ->select('ip_router', 'name')
            ->get();
        $resellers = HotspotReseller::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->select('name', 'wa', 'id')
            ->get();
        $profiles = HotspotProfile::where('group_id', $request->user()->id_group)
            ->select('id', 'name', 'price')
            ->get();
        $remarks = HotspotUser::where('group_id', $request->user()->id_group)
            ->select('created_at')
            ->distinct()
            ->latest('created_at')
            ->get();

        return view('hotspot.users.index', compact('profiles', 'nas', 'resellers', 'remarks', 'totalUsers', 'totalNew', 'totalActive', 'totalExpired', 'totalDisabled'));
    }

    public function datatable(Request $request)
    {
        if (request()->ajax()) {
            $remark = request()->get('remark');
            $status = request()->get('status');
            $nas = request()->get('nas');
            $reseller = request()->get('reseller');

            if ($remark !== null || $status !== null || $nas !== null ||  $reseller !== null) {
                if ($remark !== null && $status === null && $nas === null && $reseller === null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('created_at', $remark)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark === null && $status !== null && $nas === null && $reseller === null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('status', $status)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark === null && $status === null && $nas !== null && $reseller === null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('nas', $nas)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark === null && $status === null && $nas === null && $reseller !== null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('reseller_id', $reseller)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark !== null && $status !== null && $nas === null && $reseller === null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('created_at', $remark)
                        ->where('status', $status)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark !== null && $status === null && $nas !== null && $reseller === null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('created_at', $remark)
                        ->where('nas', $nas)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark !== null && $status === null && $nas === null && $reseller !== null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('created_at', $remark)
                        ->where('reseller_id', $reseller)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark === null && $status !== null && $nas !== null && $reseller !== null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('status', $status)
                        ->where('nas', $nas)
                        ->where('reseller_id', $reseller)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } elseif ($remark === null && $status !== null && $nas === null && $reseller !== null) {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('status', $status)
                        ->where('reseller_id', $reseller)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                } else {
                    $users = HotspotUser::query()
                        ->where('group_id', $request->user()->id_group)
                        ->where('created_at', $remark)
                        ->where('status', $status)
                        ->where('nas', $nas)
                        ->where('reseller_id', $reseller)
                        ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                        ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                }
                return DataTables::of($users)
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
                    })
                    ->toJson();
            } else {
                $selectedIds = request()->get('idsel') ?? [];
                $users = HotspotUser::query()
                    ->where('group_id', $request->user()->id_group)
                    ->with('radius:name,ip_router', 'rprofile:name', 'reseller:id,name')
                    ->select('id', 'username', 'value', 'profile', 'nas', 'server', 'created_at', 'admin', 'status', 'reseller_id');
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($row) use ($selectedIds) {
                        $checked = in_array($row->id, $selectedIds) ? ' checked' : ''; // Periksa apakah ID ada dalam array
                        return '<input type="checkbox" class="row-cb form-check-input" id="checkbox_row' . $row->id . '" value="' . $row->id . '"' . $checked . ' />';
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
                    })
                    ->toJson();
            }
        }
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jml_voucher' => 'required',
            'profile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $jml_voucher = $request->jml_voucher;
        if (
            (int) HotspotUser::where('group_id', $request->user()->id_group)->count() + (int) $jml_voucher <
            License::where('id', $request->user()->license_id)
            ->select('limit_hs')
            ->first()->limit_hs
        ) {
            $data = [];
            for ($x = 0; $x < $jml_voucher; $x++) {
                if ($request->character === '1') {
                    $characters = '0123456789';
                } elseif ($request->character === '2') {
                    $characters = 'abcdefghijklmnopqrstuvwxyz';
                } elseif ($request->character === '3') {
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                } elseif ($request->character === '4') {
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                } elseif ($request->character === '5') {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                }
                $length = $request->length;
                $charactersLength = strlen($characters);
                $user = $request->prefix;
                for ($i = 0; $i < $length; $i++) {
                    $user .= $characters[rand(0, $charactersLength - 1)];
                }
                if ($request->model === '1') {
                    $pass = $user;
                } else {
                    $pass = '';
                    for ($i = 0; $i < $length; $i++) {
                        $pass .= $characters[rand(0, $charactersLength - 1)];
                    }
                    $pass = $pass;
                }
                $draw = [
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $user,
                    'value' => $pass,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'server' => $request->hotspot_server,
                    'status' => 1,
                    'reseller_id' => $request->reseller_id,
                    'statusPayment' => $request->payment_status,
                    'created_at' => Carbon::now(),
                    'admin' => $request->user()->username,
                ];
                $data[] = $draw;
            }
            $insert_data = collect($data);
            $inserts = $insert_data->chunk(500);
            foreach ($inserts as $key => $insert) {
                $inserted = HotspotUser::insert($insert->toArray());
            }

            if ($request->payment_status === '2' && $request->total !== '0') {
                $price = str_replace('.', '', $request->total);
                $transaksi = Transaksi::create([
                    'group_id' => $request->user()->id_group,
                    'invoice_id' => 0,
                    'type' => TransactionTypeEnum::INCOME,
                    'category' => TransactionCategoryEnum::HOTSPOT,
                    'item' => 'Hotspot',
                    'deskripsi' => 'Payment Voucher Generate #' . Carbon::now()->format('d/m/Y H:i:s'),
                    'price' => $price,
                    'tanggal' => Carbon::now(),
                    'payment_method' => 1,
                    'admin' => $request->user()->username,
                ]);
                activity()
                    ->tap(function (Activity $activity) use ($request) {
                        $activity->group_id = $request->user()->id_group;
                    })
                    ->event('Create')
                    ->log('Create Transaction Pemasukan: ' . $transaksi->deskripsi . '');
            }
            // if($request->reseller_id !== null){
            // }

            // $username = [];
            // foreach($data as $index => $row){
            //     $username[$index]= $row['username'];
            // }
            // $selected_id = HotspotUser::whereIn('username',$username)->select('id')->get();

            $remark = $data[0]['created_at'];
            $selected_id = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('created_at', $remark)
                ->select('id')
                ->get();
            $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
            $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 2)
                ->count();
            $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 3)
                ->count();
            $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 0)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Voucher Berhasil Dibuat',
                'id' => $selected_id,
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

    public function destroy(Request $request)
    {
        $username = HotspotUser::whereIn('id', $request->ids)->select('username')->first();
        $username = RadiusSession::where('shortname', $request->user()->shortname)
            ->whereIn('username', $username)
            ->delete();
        $user = HotspotUser::whereIn('id', $request->ids)->delete();
        $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();
        $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();
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
        $sessions = RadiusSession::where('shortname', $request->user()->shortname)
            ->where('username', $request->username)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:3', 'max:255', Rule::unique('db_radius.user_hs')->where('group_id', $request->user()->id_group)],
            'password' => ['required', 'string', 'min:3', 'max:255'],
            // 'profile_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        if (
            HotspotUser::where('group_id', $request->user()->id_group)->count() <
            License::where('id', $request->user()->license_id)
            ->select('limit_hs')
            ->first()->limit_hs
        ) {
            $user = HotspotUser::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'server' => $request->hotspot_server,
                'status' => 1,
                'reseller_id' => $request->reseller_id,
                'statusPayment' => $request->payment_status,
                'admin' => $request->user()->username,
            ]);
            if ($request->payment_status === '2' && $request->total !== '0') {
                $price = str_replace('.', '', $request->total);
                $transaksi = Transaksi::create([
                    'group_id' => $request->user()->id_group,
                    'invoice_id' => 0,
                    'type' => TransactionTypeEnum::INCOME,
                    'category' => TransactionCategoryEnum::HOTSPOT,
                    'item' => 'Hotspot',
                    'deskripsi' => "Payment Hotspot User #$user->username",
                    'price' => $price,
                    'tanggal' => Carbon::now(),
                    'payment_method' => 1,
                    'admin' => $request->user()->username,
                ]);
                activity()
                    ->tap(function (Activity $activity) use ($request) {
                        $activity->group_id = $request->user()->id_group;
                    })
                    ->event('Create')
                    ->log('Create Transaction Pemasukan: ' . $transaksi->deskripsi . '');
            }
            $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
            $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 2)
                ->count();
            $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 3)
                ->count();
            $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
                ->where('status', 0)
                ->count();
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
            'username' => 'required|string|min:5',
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
        $user = HotspotUser::whereIn('id', $request->ids)
            ->with('rprofile')
            ->orderBy('id', 'desc')
            ->get();
        $html = view('hotspot.prints.default', compact('user'))->render();

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

        $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();
        $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();

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

        $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();
        $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();

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
        $username = RadiusSession::where('shortname', $request->user()->shortname)
            ->whereIn('username', $username)
            ->delete();

        $totaluser = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalsuspend = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 3)
            ->count();
        $totaldisabled = HotspotUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();

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

    public function getProfile(Request $request)
    {
        $profile = HotspotProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->profile)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
}
