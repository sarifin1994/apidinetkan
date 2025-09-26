<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Dinetkan\SettingsDinetkanController;
use App\Models\SettingTable;
use App\Settings\SiteSettings;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Whatsapp\Mpwa;
use Illuminate\Support\Facades\Auth;
use App\Models\Radius\RadiusSession;
use App\Models\Owner\License;

class UserController extends Controller
{
    public function index()
    {
        if (multi_auth()->role === 'Owner') {
            $setting = SettingTable::query()->where('group', 'site')->where('name', 'allow_register')->first();
            $license = License::get();
            $totaluser = User::where('role', 'Admin')->where('is_dinetkan', '!=', 1)->count();
            $usertrial = User::where('role', 'Admin')->where('license_id', 1)->where('is_dinetkan', '!=', 1)->count();
            $useractive = User::where('role', 'Admin')->whereNot('license_id', 1)->where('status', 1)->where('is_dinetkan', '!=', 1)->count();
            $userexpired = User::where('role', 'Admin')->where('status', 3)->whereNot('license_id', 1)->where('is_dinetkan', '!=', 1)->count();
            $income = User::where('role', 'Admin')
                ->whereYear('next_due', now()->year)
                ->whereMonth('next_due', now()->addMonth()->month)
                ->whereNot('license_id', 1)
                ->where('is_dinetkan', '!=', 1)
                ->with('license')
                ->get()
                ->sum(function ($user) {
                    return optional($user->license)->price - $user->discount ?? 0;
                });
            $income_unpaid = User::where('role', 'Admin')
                ->whereYear('next_due', now()->year)
                ->whereMonth('next_due', now()->month)
                ->whereNot('license_id', 1)
                ->where('is_dinetkan', '!=', 1)
                ->with('license')
                ->get()
                ->sum(function ($user) {
                    return optional($user->license)->price - $user->discount ?? 0;
                });
            // dd($income);
            if (request()->ajax()) {
                $users = User::query()
                    ->with('license', 'order_license')
                    ->withCount(['nas'])
                    ->where('role', 'Admin')
                    ->where('is_dinetkan', '!=', 1);
                // ->selectRaw('(SELECT COUNT(DISTINCT username) FROM frradius_auth.user_session WHERE type = 1 AND shortname=users.shortname AND status = 1) as hs_online')
                // ->selectRaw('(SELECT COUNT(DISTINCT username) FROM frradius_auth.user_session WHERE type = 2 AND shortname=users.shortname AND status = 1) as pppoe_online');
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->editColumn('order_name', function ($user) {
                        return $user->order_license->name;
                    })
                    ->editColumn('next_due', function ($user) {
                        return $user->next_due ? Carbon::parse($user->next_due)->translatedFormat('d F Y') : '-';
                    })
                    ->addColumn('hs_count', function ($user) {
                        return number_format($user->hs_count, 0, ',', '.') . ' User'; // Format ribuan
                    })
                    ->addColumn('pppoe_count', function ($user) {
                        return number_format($user->pppoe_count, 0, ',', '.') . ' User'; // Format ribuan
                    })
                    ->addColumn('nas_count', function ($user) {
                        return number_format($user->nas_count, 0, ',', '.') . ' Mikrotik'; // Format ribuan
                    })
                    ->addColumn('hs_online', function ($user) {
                        return number_format($user->hs_online, 0, ',', '.');
                    })
                    ->addColumn('pppoe_online', function ($user) {
                        return number_format($user->pppoe_online, 0, ',', '.');
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="javascript:void(0)" id="renew"
                                data-id="' . $row->id . '"
                                data-shortname="' . $row->shortname . '"
                                data-next_due="' . $row->next_due . '"
                                class="btn btn-success text-white"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-refresh"></i>
                            </a>
                            <a href="javascript:void(0)" id="upgrade"
                                data-id="' . $row->id . '"
                                data-order="' . $row->order . '"
                                data-shortname="' . $row->shortname . '"
                                data-next_due="' . $row->next_due . '"
                                class="btn btn-primary text-white"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-arrow-up-circle"></i>
                            </a>
                            <a href="javascript:void(0)" id="edit"
                                data-id="' . $row->id . '"
                                class="btn btn-secondary text-white"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="javascript:void(0)" id="disable"
                                data-id="' . $row->id . '"
                                class="btn btn-warning text-white"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-user-off"></i>
                            </a>
                            <a href="javascript:void(0)" id="delete"
                                data-id="' . $row->id . '"
                                data-shortname="' . $row->shortname . '"
                                class="btn btn-danger text-white"
                                style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                <i class="ti ti-trash"></i>
                            </a>';
                    })
                    ->toJson();
            }
            return view('backend.user.owner_new', compact('license', 'totaluser', 'usertrial', 'useractive', 'userexpired', 'income', 'income_unpaid', 'setting'));
        } else {
            if (request()->ajax()) {
                $users = User::query()->where('shortname', multi_auth()->shortname);
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $buttons = '<div class="d-flex flex-wrap gap-1">';

                        // Tombol Edit
                        $buttons .= '
                        <a href="javascript:void(0)" id="edit"
                            data-id="' . $row->id . '"
                            class="btn btn-secondary btn-sm"
                            title="Edit">
                            <i class="ti ti-edit"></i>
                        </a>';

                        // Hanya tampilkan tombol enable/disable dan delete jika bukan Admin
                        if ($row->role !== 'Admin') {
                            if ($row->status === 1) {
                                // Tombol Nonaktifkan
                                $buttons .= '
                                <a href="javascript:void(0)" id="disable"
                                    data-id="' . $row->id . '"
                                    class="btn btn-warning btn-sm"
                                    title="Nonaktifkan">
                                    <i class="ti ti-user-x"></i>
                                </a>';
                            } else {
                                // Tombol Aktifkan
                                $buttons .= '
                                <a href="javascript:void(0)" id="enable"
                                    data-id="' . $row->id . '"
                                    class="btn btn-success btn-sm"
                                    title="Aktifkan">
                                    <i class="ti ti-user-check"></i>
                                </a>';
                            }

                            // Tombol Hapus
                            $buttons .= '
                            <a href="javascript:void(0)" id="delete"
                                data-id="' . $row->id . '"
                                class="btn btn-danger btn-sm"
                                title="Hapus">
                                <i class="ti ti-trash"></i>
                            </a>';
                        }

                        $buttons .= '</div>';

                        return $buttons;
                    })


                    //         } else {
                    //             return '
                    //     <a href="javascript:void(0)" id="edit"
                    //     data-id="' .
                    //                 $row->id .
                    //                 '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    //         <span class="material-symbols-outlined">edit</span>
                    // </a>
                    // <a href="javascript:void(0)" id="enable" data-id="' .
                    //                 $row->id .
                    //                 '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    // <span class="material-symbols-outlined">person_check</span>
                    // </a>
                    //  <a href="javascript:void(0)" id="delete" data-id="' .
                    //                         $row->id .
                    //                         '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    //             <span class="material-symbols-outlined">delete</span>
                    //             </a>';

                    //         }

                    ->toJson();
            }
            return view('backend.user.index_new');
        }
    }

    public function getTotalSessionHotspot(Request $request)
    {
        $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', $request->shortname)->groupBy('username');
        $online = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            })
            ->where([
                ['user_session.shortname', '=', $request->shortname],
                ['user_session.status', '=', 1],
                ['user_session.type', '=', 1],
                ['user_session.stop', '=', null], // hanya yang belum stop
            ]);

        // Menghitung total sesi yang diambil berdasarkan join subquery di atas
        $total_session = $online->count();

        return response()->json([
            'total_session' => $total_session,
        ]);
    }

    public function getTotalSessionPppoe(Request $request)
    {
        $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', $request->shortname)->groupBy('username');
        $online = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            })
            ->where([
                ['user_session.shortname', '=', $request->shortname],
                ['user_session.status', '=', 1],
                ['user_session.type', '=', 2],
                ['user_session.stop', '=', null], // hanya yang belum stop
            ]);

        // Menghitung total sesi yang diambil berdasarkan join subquery di atas
        $total_session = $online->count();

        return response()->json([
            'total_session' => $total_session,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'regex:/^[a-z0-9]+$/', 'min:5', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'whatsapp' => ['required', 'string', 'min:10', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $user = User::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => 1,
            'license_id' => multi_auth()->license_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $user,
        ]);
    }

    public function show(User $user)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (multi_auth()->role === 'Owner') {
            if ($request->password === null) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                    'username' => $request->username,
                    'license_id' => $request->license_id,
                    'next_due' => $request->next_due,
                    'status' => $request->status,
                    'discount' => $request->discount,
                ]);
            } else {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'license_id' => $request->license_id,
                    'next_due' => $request->next_due,
                    'status' => $request->status,
                    'discount' => $request->discount,
                ]);
            }
        } else {
            if ($request->password === null) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                    'username' => $request->username,
                ]);
            } else {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                ]);
                Auth::guard('web')->logout();
                Auth::guard('mitra')->logout();
            }
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $user = User::where('id', $request->id);
        $user->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dinonaktifkan',
            'data' => $user,
        ]);
    }

    public function enable(Request $request)
    {
        $user = User::where('id', $request->id);
        $user->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Diaktifkan',
            'data' => $user,
        ]);
    }

    // public function order(Request $request)
    // {
    //     $user = User::where('shortname', multi_auth()->shortname);
    //     $user->update([
    //         'order_license_id' => $request->id,
    //     ]);
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Checkout Berhasil',
    //         'data' => $user,
    //     ]);
    // }

    // public function activate(Request $request)
    // {
    //     $user = User::where('shortname', $request->shortname);
    //     $next_due = Carbon::createFromFormat('Y-m-d', $request->next_due)->addMonthsWithNoOverflow(1)->toDateString();
    //     $user->update([
    //         'status' => 1,
    //         'license_id' => $request->license_id,
    //         'next_due' => $next_due,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'License Berhasil Diaktifkan!',
    //         'data' => $user,
    //     ]);
    // }

    public function renew(Request $request)
    {
        $user = User::where('shortname', $request->shortname)->with('license')->first();
        if ($user->license_id == 1) {
            $paid_date = Carbon::today();
            $next_due = Carbon::parse($paid_date)->copy()->addMonth();
        } else {
            $next_due = Carbon::parse($user->next_due)->copy()->addMonth();
        }
        $user_update = User::where('shortname', $request->shortname);
        $user_update->update([
            'status' => 1,
            'next_due' => $next_due,
            'order_status' => 'paid',
            'order' => null,
        ]);
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
            'message' => 'License Berhasil Direnew',
            'data' => $user,
        ]);
    }
    public function upgrade(Request $request)
    {
        $user = User::where('shortname', $request->shortname)->with('license', 'order_license')->first();
        $user_update = User::where('shortname', $request->shortname);
        $next_due = Carbon::now()->addMonthsWithNoOverflow(1)->toDateString();
        if ($request->order !== null) {
            $user_update->update([
                'license_id' => $request->order,
                'next_due' => $next_due,
                'order' => null,
                'order_status' => 'paid',
            ]);
        }

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
            'message' => 'License Berhasil Diupgrade',
            'data' => $user,
        ]);
    }

    public function delete(Request $request)
    {
        \App\Models\Hotspot\HotspotUser::where('shortname', $request->shortname)->delete();
        \App\Models\Hotspot\HotspotProfile::where('shortname', $request->shortname)->delete();
        \App\Models\Invoice\Invoice::where('shortname', $request->shortname)->delete();
        \App\Models\Keuangan\Transaksi::where('shortname', $request->shortname)->delete();
        \App\Models\Keuangan\KategoriKeuangan::where('shortname', $request->shortname)->delete();
        \App\Models\Mapping\Odp::where('shortname', $request->shortname)->delete();
        \App\Models\Mapping\Pop::where('shortname', $request->shortname)->delete();
        \App\Models\Mikrotik\Nas::where('shortname', $request->shortname)->delete();
        \App\Models\Olt\OltDevice::where('shortname', $request->shortname)->delete();
        \App\Models\Partnership\Mitra::where('shortname', $request->shortname)->delete();
        \App\Models\Pppoe\PppoeUser::where('shortname', $request->shortname)->delete();
        \App\Models\Pppoe\PppoeProfile::where('shortname', $request->shortname)->delete();
        \App\Models\Radius\RadiusNas::where('shortname', $request->shortname)->delete();
        \App\Models\Mikrotik\Nas::where('shortname', $request->shortname)->delete();
        \App\Models\Radius\RadiusProfile::where('shortname', $request->shortname)->delete();
        \App\Models\Radius\RadiusSession::where('shortname', $request->shortname)->delete();
        \App\Models\Setting\BillingSetting::where('shortname', $request->shortname)->delete();
        \App\Models\Setting\Company::where('shortname', $request->shortname)->delete();
        \App\Models\Setting\Isolir::where('shortname', $request->shortname)->delete();
        \App\Models\Setting\Midtrans::where('shortname', $request->shortname)->delete();
        \App\Models\Whatsapp\Mpwa::where('shortname', $request->shortname)->delete();
        \App\Models\Whatsapp\Watemplate::where('shortname', $request->shortname)->delete();
        \App\Models\ActivityLog::where('shortname', $request->shortname)->delete();
        $user = User::where('shortname', $request->shortname)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Berhasil Dihapus',
            'data' => $user,
        ]);
    }

    public function loginAsUser(User $user)
    {
        session(['origin_id' => Auth::id()]);
        Auth::login($user);
        return redirect()->to('/')->with('success', 'You are now logged in as ' . $user->name);
    }

    public function set_allow_register(){
        $setting = SettingTable::query()->where('group', 'site')->where('name', 'allow_register')->first();
        if($setting->payload == "1"){
            $setting->update([
                'payload' => "0"
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Seting Berhasil diUpdate',
                'data' => [],
            ]);
        }
        if($setting->payload == "0"){
            $setting->update([
                'payload' => "1"
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Seting Berhasil diUpdate',
                'data' => [],
            ]);
        }
    }
}
