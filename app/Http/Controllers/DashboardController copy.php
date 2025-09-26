<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Radius\RadiusSession;
use App\Models\Invoice\Invoice;
use Carbon\Carbon;
use App\Models\Keuangan\Transaksi;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Mikrotik\Nas;
use App\Models\ActivityLog;
use Yajra\DataTables\Facades\DataTables;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Hotspot\HotspotUser;
use App\Models\Pppoe\PppoeUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (multi_auth()->role === 'Admin') {
            $hotspotonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 1], ['stop', null]])
                ->count();
            $pppoeonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 2], ['stop', null]])
                ->count();
            $totalunpaid = Invoice::query()
                ->where('shortname', multi_auth()->shortname)
                ->whereYear('due_date', Carbon::today()->year)
                ->whereMonth('due_date', Carbon::today()->month)
                ->where('status', 'unpaid')
                ->count();
            $incometoday = Transaksi::query()->where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', Carbon::today())->sum('nominal');

            $bulan = date('m');
            $tahun = date('Y');
            for ($i = 1; $i <= $bulan; $i++) {
                $dataBulan[] = Carbon::create()->month($i)->format('F');
                $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
                $dataTotalIncome[] = $totalIncome;
                $totalExpense = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
                $dataTotalExpense[] = $totalExpense;
            }
            $dataIncome = [
                'labels' => $dataBulan,
                'data' => $dataTotalIncome,
            ];
            $dataExpense = [
                'labels' => $dataBulan,
                'data' => $dataTotalExpense,
            ];

            $activity = ActivityLog::where('shortname', multi_auth()->shortname)->orderBy('id', 'desc')->limit(5)->get();
            if (request()->ajax()) {
                $nas = Nas::query()
                    ->selectRaw('radius_mikrotik.*, count(distinct user_session.username) as unique_ronline')
                    ->leftJoin('user_session', function ($join) {
                        $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')->where('user_session.status', 1)->whereNull('user_session.stop');
                    })
                    ->where('radius_mikrotik.shortname', multi_auth()->shortname)
                    ->groupBy('radius_mikrotik.id')
                    ->limit(5);
                return DataTables::of($nas)
                    ->addIndexColumn()
                    ->addColumn('online', function ($row) {
                        return $row->unique_ronline;
                    })
                    ->addColumn('ping', function ($row) {
                        return '<span data-id="'.$row->id.
                        '" class="ping-check material-symbols-outlined spinner">progress_activity</span>'; // atau 'Loading...'
                    })
                    ->rawColumns(['ping','action']) 
                    ->addColumn('action', function ($row) {
                        return '<a href="javascript:void(0)" id="show"
                    data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">markdown_paste</span> Script
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                            $row->id .
                            '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                    })
                    ->toJson();
            }

            return view('backend.dashboard.admin', compact('hotspotonline', 'pppoeonline', 'totalunpaid', 'incometoday', 'dataIncome', 'dataExpense', 'activity'));
        } elseif (multi_auth()->role === 'Teknisi') {
            $hotspotonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 1], ['stop', null]])
                ->count();
            $pppoeonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 2], ['stop', null]])
                ->count();
            $hotspottotal = HotspotUser::where('shortname', multi_auth()->shortname)->count();
            $pppoetotal = PppoeUser::where('shortname', multi_auth()->shortname)->count();

            if (request()->ajax()) {
                $nas = Nas::query()
                    ->selectRaw('radius_mikrotik.*, count(distinct user_session.username) as unique_ronline')
                    ->leftJoin('user_session', function ($join) {
                        $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')->where('user_session.status', 1)->whereNull('user_session.stop');
                    })
                    ->where('radius_mikrotik.shortname', multi_auth()->shortname)
                    ->groupBy('radius_mikrotik.id');
                return DataTables::of($nas)
                    ->addIndexColumn()
                    ->addColumn('online', function ($row) {
                        return $row->unique_ronline;
                    })
                    ->addColumn('ping', function ($row) {
                        try {
                            $client = new Client([
                                'host' => $row->ip_router,
                                'user' => $row->user,
                                'pass' => $row->password,
                                'port' => $row->port_api,
                            ]);
                        } catch (\Exception $e) {
                            return $e->getMessage();
                        }
                        $query = new Query('/system/identity/print');
                        $response = $client->query($query)->read();
                        if ($response) {
                            return 1;
                        } else {
                            return 0;
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="javascript:void(0)" id="show"
                    data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">markdown_paste</span> Script
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                            $row->id .
                            '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                    })
                    ->toJson();
            }

            return view('backend.dashboard.teknisi', compact('hotspotonline', 'pppoeonline', 'hotspottotal', 'pppoetotal'));
        } elseif (multi_auth()->role === 'Kasir') {
            $hotspotonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 1], ['stop', null]])
                ->count();
            $pppoeonline = RadiusSession::select(['session_id', 'username', 'ip', 'mac', 'input', 'output', 'uptime', 'start', 'stop'])
                ->where('shortname', multi_auth()->shortname)
                ->distinct('username')
                ->where([['status', 1], ['type', 2], ['stop', null]])
                ->count();
            $totalunpaid = Invoice::query()
                ->where('shortname', multi_auth()->shortname)
                ->whereYear('due_date', Carbon::today()->year)
                ->whereMonth('due_date', Carbon::today()->month)
                ->where('status', 'unpaid')
                ->count();
            $totalpaid = Invoice::query()
                ->where('shortname', multi_auth()->shortname)
                ->whereYear('due_date', Carbon::today()->year)
                ->whereMonth('due_date', Carbon::today()->month)
                ->where('status', 'paid')
                ->count();
            $totaltagihan = Invoice::query()
                ->where('shortname', multi_auth()->shortname)
                ->whereYear('due_date', Carbon::today()->year)
                ->whereMonth('due_date', Carbon::today()->month)
                ->where('status', 'unpaid')
                ->sum('price');
            $incometoday = Transaksi::query()->where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', Carbon::today())->sum('nominal');

            $bulan = date('m');
            $tahun = date('Y');
            for ($i = 1; $i <= $bulan; $i++) {
                $dataBulan[] = Carbon::create()->month($i)->format('F');
                $totalIncome = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pemasukan')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
                $dataTotalIncome[] = $totalIncome;
                $totalExpense = Transaksi::where('shortname', multi_auth()->shortname)->where('tipe', 'Pengeluaran')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
                $dataTotalExpense[] = $totalExpense;
            }
            $dataIncome = [
                'labels' => $dataBulan,
                'data' => $dataTotalIncome,
            ];
            $dataExpense = [
                'labels' => $dataBulan,
                'data' => $dataTotalExpense,
            ];

            $activity = ActivityLog::where('shortname', multi_auth()->shortname)->orderBy('id', 'desc')->limit(5)->get();
            if (request()->ajax()) {
                $nas = Nas::query()
                    ->selectRaw('radius_mikrotik.*, count(distinct user_session.username) as unique_ronline')
                    ->leftJoin('user_session', function ($join) {
                        $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')->where('user_session.status', 1)->whereNull('user_session.stop');
                    })
                    ->where('radius_mikrotik.shortname', multi_auth()->shortname)
                    ->groupBy('radius_mikrotik.id');
                return DataTables::of($nas)
                    ->addIndexColumn()
                    ->addColumn('online', function ($row) {
                        return $row->unique_ronline;
                    })
                    ->addColumn('ping', function ($row) {
                        try {
                            $client = new Client([
                                'host' => $row->ip_router,
                                'user' => $row->user,
                                'pass' => $row->password,
                                'port' => $row->port_api,
                            ]);
                        } catch (\Exception $e) {
                            return $e->getMessage();
                        }
                        $query = new Query('/system/identity/print');
                        $response = $client->query($query)->read();
                        if ($response) {
                            return 1;
                        } else {
                            return 0;
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="javascript:void(0)" id="show"
                    data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">markdown_paste</span> Script
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                            $row->id .
                            '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                    })
                    ->toJson();
            }

            return view('backend.dashboard.kasir', compact('totalunpaid', 'totalpaid', 'totaltagihan', 'incometoday', 'dataIncome', 'dataExpense'));
        } elseif (multi_auth()->role === 'Mitra') {
            $pppoetotal = PppoeUser::where('shortname', multi_auth()->shortname)->where('mitra_id', multi_auth()->id)->count();
            $pppoepending = PppoeUser::where('shortname', multi_auth()->shortname)->where('mitra_id', multi_auth()->id)->where('status',0)->count();
            $totalkomisi = TransaksiMitra::query()
                ->where('shortname', multi_auth()->shortname)
                ->where('mitra_id',multi_auth()->id)
                ->whereYear('tanggal', Carbon::today()->year)
                ->whereMonth('tanggal', Carbon::today()->month)
                ->sum('nominal');
            $totalunpaid = Invoice::query()
                ->where('shortname', multi_auth()->shortname)
                ->where('mitra_id',multi_auth()->id)
                ->where('status','unpaid')
                ->whereYear('due_date', Carbon::today()->year)
                ->whereMonth('due_date', Carbon::today()->month)
                ->count();

            if (request()->ajax()) {
                $nas = Nas::query()
                    ->selectRaw('radius_mikrotik.*, count(distinct user_session.username) as unique_ronline')
                    ->leftJoin('user_session', function ($join) {
                        $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')->where('user_session.status', 1)->whereNull('user_session.stop');
                    })
                    ->where('radius_mikrotik.shortname', multi_auth()->shortname)
                    ->groupBy('radius_mikrotik.id');
                return DataTables::of($nas)
                    ->addIndexColumn()
                    ->addColumn('online', function ($row) {
                        return $row->unique_ronline;
                    })
                    ->addColumn('ping', function ($row) {
                        try {
                            $client = new Client([
                                'host' => $row->ip_router,
                                'user' => $row->user,
                                'pass' => $row->password,
                                'port' => $row->port_api,
                            ]);
                        } catch (\Exception $e) {
                            return $e->getMessage();
                        }
                        $query = new Query('/system/identity/print');
                        $response = $client->query($query)->read();
                        if ($response) {
                            return 1;
                        } else {
                            return 0;
                        }
                    })
                    ->addColumn('action', function ($row) {
                        return '<a href="javascript:void(0)" id="show"
                    data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">markdown_paste</span> Script
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                            $row->id .
                            '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                    })
                    ->toJson();
            }
            return view('backend.dashboard.mitra', compact('pppoepending', 'pppoetotal','totalkomisi','totalunpaid'));
        } elseif (multi_auth()->role === 'Owner') {
            $totaluser = User::where('role', 'Admin')->count();
            $usertrial = User::where('role', 'Admin')->where('license_id', 1)->count();
            $useractive = User::where('role', 'Admin')->whereNot('license_id', 1)->where('status', 1)->count();
            $userexpired = User::where('role', 'Admin')->where('status', 2)->count();
            $serverInfo = [
                'OS' => trim(shell_exec('lsb_release -d | cut -f2')),
                'Hostname' => trim(shell_exec('hostname')),
                'PHP Version' => PHP_VERSION,
                'Laravel Version' => app()->version(),
                'Model Name' => trim(shell_exec("lscpu | grep 'Model name' | awk -F: '{print $2}'")),
                'CPU Cores' => trim(shell_exec("lscpu | grep '^CPU(s):' | awk '{print $2}'")),
                'CPU MHz' => trim(shell_exec("lscpu | grep 'CPU MHz' | awk -F: '{print $2}'")),
                'CPU Architecture' => trim(shell_exec("lscpu | grep 'Architecture' | awk -F: '{print $2}'")),
                'Total Memory (MB)' => trim(shell_exec("free -m | awk 'NR==2{print $2}'")),
                'Used Memory (MB)' => trim(shell_exec("free -m | awk 'NR==2{print $3}'")),
                'Free Memory (MB)' => trim(shell_exec("free -m | awk 'NR==2{print $4}'")),
                'Disk Total' => trim(shell_exec("df -h / | awk 'NR==2{print $2}'")),
                'Disk Used' => trim(shell_exec("df -h / | awk 'NR==2{print $3}'")),
                'Disk Available' => trim(shell_exec("df -h / | awk 'NR==2{print $4}'")),
                'Disk Usage' => trim(shell_exec("df -h / | awk 'NR==2{print $5}'")),
            ];

            // Cek Status MySQL
            try {
                DB::connection()->getPdo();
                $mysqlStatus = 'Active';
                $mysqlConnections = DB::select("SHOW STATUS WHERE Variable_name = 'Threads_connected'")[0]->Value;
            } catch (\Exception $e) {
                $mysqlStatus = 'Inactive';
                $mysqlConnections = 'N/A';
            }

            $radiusStatus = trim(shell_exec("ss -tulpn | grep ':1812 '")) ? 'active' : 'inactive';

            return view('backend.dashboard.owner', compact('totaluser', 'usertrial', 'useractive', 'userexpired', 'serverInfo','mysqlStatus','mysqlConnections','radiusStatus'));
        }
    }
}
