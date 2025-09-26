<?php

/**
 * @deprecated Use the `App\Http\Controllers\Service\DataController` class instead.
 * @see App\Http\Controllers\Service\DataController
 *
 * This class is only used for backward compatibility and will be removed in the future.
 */

namespace App\Http\Controllers\Pppoe;

use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Nas;
use App\Models\Odp;
use App\Models\Area;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\License;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use App\Models\PppoeProfile;
use Illuminate\Http\Request;
use App\Models\RadiusSession;
use App\Models\BillingSetting;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Process;
use Maatwebsite\Excel\Concerns\ToModel;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalActive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalSuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totalDisabled = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();

        $areas = Area::where('group_id', $request->user()->id_group)
            ->select('id', 'kode_area')
            ->orderBy('kode_area', 'desc')
            ->get();
        $nas = Nas::where('group_id', $request->user()->id_group)
            ->select('ip_router', 'name')
            ->get();
        $profiles = PppoeProfile::where('group_id', $request->user()->id_group)
            ->select('id', 'name', 'price')
            ->get();

        if (request()->ajax()) {
            $users = PppoeUser::query()
                ->where('group_id', $request->user()->id_group)
                ->with('member:pppoe_id,full_name', 'radius:name,ip_router', 'rprofile:id,name', 'rarea:kode_area,id', 'rodp:kode_odp', 'session:username,session_id,ip,status');
            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('nas_name', function ($row) {
                    return $row->radius->name;
                })
                ->editColumn('profile_name', function ($row) {
                    return $row->rprofile->name;
                })
                ->editColumn('area_name', function ($row) {
                    return $row->rarea->kode_area;
                })
                ->editColumn('odp_name', function ($row) {
                    return $row->rodp->kode_odp;
                })
                ->editColumn('session_internet', function ($row) {
                    return $row->session->session_id;
                })
                ->editColumn('billing', function ($row) {
                    return Member::where('member.pppoe_id', $row->id)->count();
                })
                ->toJson();
        }
        return view('pppoe.users.index', compact('areas', 'nas', 'profiles', 'totalUsers', 'totalActive', 'totalSuspend', 'totalDisabled'));
    }

    public function show(PppoeUser $user)
    {
        $data = PppoeUser::with('radius', 'rprofile', 'rarea', 'rodp')->find($user->id);
        // $profile_id = Member::where('pppoe_id',$user->id)->select('profile_id')->first()->profile_id;
        return response()->json([
            'success' => true,
            'data' => $data,
            // 'profile_id' => $profile_id,
        ]);
    }

    public function getMember(Request $request)
    {
        $member = Member::where('id', $request->id)->get();
        return response()->json([
            'success' => true,
            'data' => $member,
        ]);
    }

    public function getKodeOdp(Request $request)
    {
        $data['odp'] = Odp::where('group_id', $request->user()->id_group)
            ->where('kode_area_id', $request->kode_area_id)
            ->orderBy('kode_odp')
            ->get(['kode_odp']);
        return response()->json($data);
    }

    public function getSession(Request $request)
    {
        $sessions = RadiusSession::where('shortname', $request->user()->shortname)
            ->where('username', $request->username)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($sessions);
    }

    public function getPrice(Request $request)
    {
        $data = PppoeProfile::where('group_id', $request->user()->id_group)
            ->where('id', $request->id)
            ->get(['price']);
        return response()->json($data);
    }

    public function enable(Request $request, PppoeUser $id)
    {
        $id->update([
            'status' => 1,
        ]);
        if ($request->nas === null) {
            $nas = RadiusNas::where('group_id', $request->user()->id_group)
                ->select('nasname', 'secret')
                ->get();
            foreach ($nas as $item) {
                $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $item[nasname]:3799 disconnect $item[secret]");
            }
        } else {
            $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $request->nas:3799 disconnect $request->secret");
        }
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Enable User PPPoE: ' . $request->username . '');
        $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $id,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function disable(Request $request, PppoeUser $id)
    {
        $id->update([
            'status' => 0,
        ]);

        if ($request->nas === null) {
            $nas = RadiusNas::where('group_id', $request->user()->id_group)
                ->select('nasname', 'secret')
                ->get();
            foreach ($nas as $item) {
                $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $item[nasname]:3799 disconnect $item[secret]");
            }
        } else {
            $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $request->nas:3799 disconnect $request->secret");
        }
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Disable User PPPoE: ' . $request->username . '');

        $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $id,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function suspend(Request $request, PppoeUser $id)
    {
        $id->update([
            'status' => 2,
        ]);

        if ($request->nas === null) {
            $nas = RadiusNas::where('group_id', $request->user()->id_group)
                ->select('nasname', 'secret')
                ->get();
            foreach ($nas as $item) {
                $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $item[nasname]:3799 disconnect $item[secret]");
            }
        } else {
            $command = Process::path('/usr/bin/')->run("echo User-Name='$request->username' | radclient -r 1 $request->nas:3799 disconnect $request->secret");
        }
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Suspend User PPPoE: ' . $request->username . '');

        $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $id,
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->option_member === '1') {
            $validator = Validator::make($request->all(), [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    Rule::unique('db_radius.user_pppoe')->where('group_id', $request->user()->id_group),
                ],
                'password' => 'required|string',
                'profile' => 'required|string',
                'full_name' => 'required|string|min:3',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                ]);
            }

            activity()
                ->tap(function (Activity $activity) use ($request) {
                    $activity->group_id = $request->user()->id_group;
                })
                ->event('Create')
                ->log('Create New User PPPoE: ' . $request->username . '');

            $today = Carbon::now()->format('Y-m-d');
            $cek_reg_date = Carbon::createFromFormat('Y-m-d', $today)->subMonthsWithNoOverflow(2)->toDateString();
            $cek_reg_date_bc = Carbon::createFromFormat('Y-m-d', $today)->startOfMonth()->subMonthsWithNoOverflow(2)->toDateString();
            $tgl = date('d', strtotime($request->reg_date));
            if (PppoeUser::where('group_id', $request->user()->id_group)->count() >= License::where('id', $request->user()->license_id)->select('limit_pppoe')->first()->limit_pppoe) {
                return response()->json([
                    'error' => 'Sorry your license is limited, please upgrade!',
                ]);
            }
            if ($request->reg_date < $cek_reg_date) {
                return response()->json([
                    'error' => 'Failed to create user, please change active date!',
                ]);
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $cek_reg_date_bc) {
                return response()->json([
                    'success' => 'Failed to create user, please change active date!',
                ]);
            } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'paid' && $request->reg_date >= $cek_reg_date) {
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $price = str_replace('.', '', $request->amount);
                $total = str_replace('.', '', $request->payment_total);
                $period = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime($period));

                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'member_name' => $request->full_name,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                $member = Member::create([
                    'group_id' => $request->user()->id_group,
                    'id_member' => rand(1314000000, 1314999999),
                    'pppoe_id' => $user->id,
                    'profile_id' => $request->profile_id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'wa' => $request->wa,
                    'address' => $request->address,
                    'kode_area' => $request->kode_area,
                    'payment_type' => $request->payment_type,
                    'payment_status' => $request->payment_status,
                    'billing_period' => $request->billing_period,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                ]);

                $no_invoice = date('m') . rand(0000000, 9999999);
                $invoice = Invoice::create([
                    'group_id' => $request->user()->id_group,
                    'member_id' => $member->id,
                    'no_invoice' => $no_invoice,
                    'item' => "Internet: $request->username | $request->profile",
                    'price' => $price,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'invoice_date' => $request->reg_date,
                    'due_date' => $request->reg_date,
                    'period' => $period,
                    'subscribe' => $awal . ' ' . 's/d' . ' ' . $akhir,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'payment_type' => $request->payment_type,
                    'billing_period' => $request->billing_period,
                    'payment_url' => route('invoice.pay', $no_invoice),
                    'paid_date' => $request->reg_date,
                    'status' => 1,
                ]);

                $transaksi = Transaksi::create([
                    'group_id' => $request->user()->id_group,
                    'invoice_id' => $invoice->id,
                    'invoice_type' => Invoice::class,
                    'type' => TransactionTypeEnum::INCOME,
                    'category' => TransactionCategoryEnum::INVOICE,
                    'item' => 'Invoice',
                    'deskripsi' => "Payment #$invoice->no_invoice a.n $request->full_name",
                    'price' => $total,
                    'tanggal' => Carbon::now(),
                    'payment_method' => 1,
                    'admin' => $request->user()->username,
                ]);

                activity()
                    ->tap(function (Activity $activity) use ($request) {
                        $activity->group_id = $request->user()->id_group;
                    })
                    ->event('Create')
                    ->log('Create New Transaction Pemasukan: ' . $transaksi->deskripsi . '');

                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'unpaid' && $request->reg_date > $cek_reg_date) {
                $next_due = $request->reg_date;
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $price = str_replace('.', '', $request->amount);
                $period = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime($period));

                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'member_name' => $request->full_name,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                $member = Member::create([
                    'group_id' => $request->user()->id_group,
                    'id_member' => rand(1314000000, 1314999999),
                    'pppoe_id' => $user->id,
                    'profile_id' => $request->profile_id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'wa' => $request->wa,
                    'address' => $request->address,
                    'kode_area' => $request->kode_area,
                    'payment_type' => $request->payment_type,
                    'payment_status' => $request->payment_status,
                    'billing_period' => $request->billing_period,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                ]);

                $no_invoice = date('m') . rand(0000000, 9999999);
                $invoice = Invoice::create([
                    'group_id' => $request->user()->id_group,
                    'member_id' => $member->id,
                    'no_invoice' => $no_invoice,
                    'item' => "Internet: $request->username | $request->profile",
                    'price' => $price,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'invoice_date' => $request->reg_date,
                    'due_date' => $request->reg_date,
                    'period' => $period,
                    'subscribe' => $awal . ' ' . 's/d' . ' ' . $akhir,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'payment_type' => $request->payment_type,
                    'billing_period' => $request->billing_period,
                    'payment_url' => route('invoice.pay', $no_invoice),
                    'paid_date' => $request->reg_date,
                    'status' => 0,
                ]);

                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date' && $request->reg_date > $cek_reg_date) {
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsWithNoOverflow(1);

                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'member_name' => $request->full_name,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                $member = Member::create([
                    'group_id' => $request->user()->id_group,
                    'id_member' => rand(1314000000, 1314999999),
                    'pppoe_id' => $user->id,
                    'profile_id' => $request->profile_id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'wa' => $request->wa,
                    'address' => $request->address,
                    'kode_area' => $request->kode_area,
                    'payment_type' => $request->payment_type,
                    'payment_status' => $request->payment_status,
                    'billing_period' => $request->billing_period,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                ]);

                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $cek_reg_date_bc) {
                $due_bc = BillingSetting::where('group_id', $request->user()->id_group)
                    ->select('due_bc')
                    ->first();
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->setDay($due_bc->due_bc)
                    ->addMonths(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->startOfMonth()
                    ->addMonths(1);
                $price = str_replace('.', '', $request->amount);
                $periode = Carbon::createFromFormat('Y-m-d', $request->reg_date)->toDateString();
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->startOfMonth()
                    ->addMonthsWithNoOverflow(1)
                    ->toDateString();
                $akhir_day = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->endOfMonth()
                    ->toDateString();

                $awal = date('d/m/Y', strtotime($request->reg_date));
                $akhir = date('d/m/Y', strtotime($akhir_day));

                $jml_day = Carbon::createFromFormat('Y-m-d', $request->reg_date)->month()->daysInMonth;
                $jml_usage = Carbon::parse($request->reg_date)->diffInDays($akhir_day);
                $daily_price0 = $price / $jml_day;
                $daily_price = number_format($daily_price0, 0, '.', '');
                $prorate = $jml_usage * $daily_price;
                $amount = number_format($prorate, 0, '.', '');

                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'member_name' => $request->full_name,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                $member = Member::create([
                    'group_id' => $request->user()->id_group,
                    'id_member' => rand(1314000000, 1314999999),
                    'pppoe_id' => $user->id,
                    'profile_id' => $request->profile_id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'wa' => $request->wa,
                    'address' => $request->address,
                    'kode_area' => $request->kode_area,
                    'payment_type' => $request->payment_type,
                    'payment_status' => $request->payment_status,
                    'billing_period' => $request->billing_period,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                ]);

                // $invoice = Invoice::create([
                //     'member_id' => $member->id,
                //     'no_invoice' => date('m') . rand(0000000, 9999999),
                //     'item' => "Internet: $request->username | $request->profile @aktif $jml_usage hari",
                //     'price' => $amount,
                //     'ppn' => $request->ppn,
                //     'discount' => $request->discount,
                //     'invoice_date' => $today,
                //     'due_date' => $next_due,
                //     'period' => $periode,
                //     'subscribe' => $awal . ' ' . 's/d' . ' ' . $akhir,
                //     'reg_date' => $request->reg_date,
                //     'next_due' => $next_due,
                //     'payment_type' => $request->payment_type,
                //     'billing_period' => $request->billing_period,
                //     'status' => 0,
                // ]);
                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date > $cek_reg_date_bc) {
                $due_bc = BillingSetting::where('group_id', $request->user()->id_group)
                    ->select('due_bc')
                    ->first();
                $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->setDay($due_bc->due_bc)
                    ->addMonths(1);
                $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)
                    ->startOfMonth()
                    ->addMonths(1);
                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'member_name' => $request->full_name,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                $member = Member::create([
                    'group_id' => $request->user()->id_group,
                    'id_member' => rand(1314000000, 1314999999),
                    'pppoe_id' => $user->id,
                    'profile_id' => $request->profile_id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'wa' => $request->wa,
                    'address' => $request->address,
                    'kode_area' => $request->kode_area,
                    'payment_type' => $request->payment_type,
                    'payment_status' => $request->payment_status,
                    'billing_period' => $request->billing_period,
                    'ppn' => $request->ppn,
                    'discount' => $request->discount,
                    'reg_date' => $request->reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                ]);
                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();
                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    Rule::unique('db_radius.user_pppoe')->where('group_id', $request->user()->id_group),
                ],
                'password' => 'required|string',
                'profile' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                ]);
            }

            if (PppoeUser::where('group_id', $request->user()->id_group)->count() >= License::where('id', $request->user()->license_id)->select('limit_pppoe')->first()->limit_pppoe) {
                return response()->json([
                    'error' => 'Sorry your license is limited, please upgrade!',
                ]);
            } else {
                $user = PppoeUser::create([
                    'group_id' => $request->user()->id_group,
                    'shortname' => $request->user()->shortname,
                    'username' => $request->username,
                    'value' => $request->password,
                    'profile' => $request->profile,
                    'nas' => $request->nas,
                    'kode_area' => $request->kode_area,
                    'kode_odp' => $request->kode_odp,
                    'lock_mac' => $request->lock_mac,
                    'mac' => $request->mac,
                    'status' => 1,
                ]);

                activity()
                    ->tap(function (Activity $activity) use ($request) {
                        $activity->group_id = $request->user()->id_group;
                    })
                    ->event('Create')
                    ->log('Create New User PPPoE: ' . $request->username . '');

                $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
                $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 1)
                    ->count();
                $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 2)
                    ->count();
                $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
                    ->where('status', 0)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'totaluser' => $totaluser,
                    'totalactive' => $totalactive,
                    'totalsuspend' => $totalsuspend,
                    'totaldisabled' => $totaldisabled,
                ]);
            }
        }
    }

    public function update(Request $request, PppoeUser $user)
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

        if ($request->lock_mac === '0') {
            $user->update([
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'kode_area' => $request->kode_area,
                'kode_odp' => $request->kode_odp,
                'lock_mac' => $request->lock_mac,
            ]);
        } else {
            $user->update([
                'username' => $request->username,
                'value' => $request->password,
                'profile' => $request->profile,
                'nas' => $request->nas,
                'kode_area' => $request->kode_area,
                'kode_odp' => $request->kode_odp,
                'lock_mac' => $request->lock_mac,
                'mac' => $request->mac,
            ]);
        }

        $member = Member::where('pppoe_id', $user->id);
        $member->update([
            'kode_area' => $request->kode_area,
            'profile_id' => $request->profile_id,
        ]);

        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Update User PPPoE: ' . $request->username . '');

        $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
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

    public function destroy(Request $request, $id)
    {
        $user = PppoeUser::findOrFail($id);
        $username = PppoeUser::where('id', $id)->select('username')->first();
        $member_id = Member::where('pppoe_id', $id)->select('id')->first();
        if ($member_id === null) {
            $user->delete();
            $username = RadiusSession::where('shortname', $request->user()->shortname)
                ->where('username', $username)
                ->delete();
        } else {
            $username = RadiusSession::where('shortname', $request->user()->shortname)
                ->where('username', $username)
                ->delete();
            $invoice = Invoice::where('member_id', $member_id->id)->delete();
            $member = Member::where('pppoe_id', $id)->delete();
            $user->delete();
        }
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Delete')
            ->log('Delete User PPPoE: ' . $username . '');
        $totaluser = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $totalactive = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 1)
            ->count();
        $totalsuspend = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 2)
            ->count();
        $totaldisabled = PppoeUser::where('group_id', $request->user()->id_group)
            ->where('status', 0)
            ->count();
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
            'totaluser' => $totaluser,
            'totalactive' => $totalactive,
            'totalsuspend' => $totalsuspend,
            'totaldisabled' => $totaldisabled,
        ]);
    }
    public function export(Request $request)
    {
        // Fetch PPPoE users from db_radius
        $pppoe_users = DB::connection('db_radius')
            ->table('user_pppoe')
            ->where('group_id', $request->user()->id_group)
            ->get();

        // Get the IDs of PPPoE users to fetch associated members
        $pppoe_user_ids = $pppoe_users->pluck('id')->toArray();

        // Fetch members from the default connection and key them by pppoe_id
        $members = Member::whereIn('pppoe_id', $pppoe_user_ids)
            ->get()
            ->keyBy('pppoe_id');

        // Prepare data for export
        $data = [];
        $data[] = [
            'Username',
            'Password',
            'Profile',
            'NAS',
            'Area',
            'ODP',
            'Lock MAC',
            'MAC',
            'Status',
            'Full Name',
            'WhatsApp',
            'Address',
            'Payment Type',
            'Payment Status',
            'Billing Period',
            'Registration Date',
            'PPN',
            'Discount'
        ];

        foreach ($pppoe_users as $user) {
            $member = $members->get($user->id);

            $data[] = [
                $user->username,
                $user->value,
                $user->profile,
                $user->nas,
                $user->kode_area,
                $user->kode_odp,
                $user->lock_mac,
                $user->mac,
                $user->status,
                $member->full_name ?? '',
                $member->wa ?? '',
                $member->address ?? '',
                $member->payment_type ?? '',
                $member->payment_status ?? '',
                $member->billing_period ?? '',
                isset($member->reg_date) ? \Carbon\Carbon::parse($member->reg_date)->format('Y-m-d') : '',
                $member->ppn ?? '',
                $member->discount ?? '',
            ];
        }

        // Export data to Excel
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, 'pppoe_users_with_billing.xlsx');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');

        try {
            Excel::import(new class($request) implements ToModel, WithValidation {
                private $request;

                public function __construct($request)
                {
                    $this->request = $request;
                }
                use Importable;

                public function model(array $row)
                {
                    // Skip header row
                    if ($row[0] == 'Username' || $row[0] == null) {
                        return null;
                    }

                    // Check if username already exists
                    if (PppoeUser::where('username', $row[0])->exists()) {
                        throw new \Exception("Username '{$row[0]}' already exists.");
                    }

                    return new PppoeUser([
                        'group_id'   => $this->request->user()->id_group,
                        'shortname'  => $this->request->user()->shortname,
                        'username'   => $row[0],
                        'value'      => $row[1],
                        'profile'    => $row[2],
                        'nas'        => $row[3],
                        'kode_area'  => $row[4],
                        'kode_odp'   => $row[5],
                        'lock_mac'   => $row[6],
                        'mac'        => $row[7],
                        'status'     => $row[8],
                    ]);
                }

                public function rules(): array
                {
                    return [
                        '*.0' => 'required|string|min:3|max:255', // Username
                        '*.1' => 'required|string',               // Password
                        '*.2' => 'required|string',               // Profile
                        '*.8' => 'required|in:0,1,2',             // Status
                        // Add other validation rules as needed
                    ];
                }

                public function customValidationMessages()
                {
                    return [
                        'required' => 'The :attribute field is required.',
                        'min' => 'The :attribute must be at least :min characters.',
                        'in' => 'The :attribute must be one of the following values: :values.',
                    ];
                }
            }, $file);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Import successful.');
    }

    public function sample()
    {
        $data = [
            ['Username', 'Password', 'Profile', 'NAS', 'Area', 'ODP', 'Lock MAC', 'MAC', 'Status'],
            ['john_doe', 'password123', 'BasicProfile', 'NAS1', 'Area1', 'ODP1', '0', '', '1'],
            ['jane_smith', 'securepass', 'PremiumProfile', '', '', '', '1', 'AA:BB:CC:DD:EE:FF', '1'],
            // Add more sample rows if needed
        ];

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, 'pppoe_users_sample.xlsx');
    }
}
