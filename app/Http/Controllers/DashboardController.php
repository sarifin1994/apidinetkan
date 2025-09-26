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
        $auth = multi_auth();
        $role = $auth->role;
        $shortname = $auth->shortname;
        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;

        if ($role === 'Admin') {
            // $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', multi_auth()->shortname)->groupBy('username');
            // $hotspotonline = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
            //     ->joinSub($sub, 'latest', function ($join) {
            //         $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            //     })
            //     ->where([
            //         ['user_session.shortname', '=', multi_auth()->shortname],
            //         ['user_session.status', '=', 1],
            //         ['user_session.type', '=', 1],
            //         ['user_session.stop', '=', null], // hanya yang belum stop
            //     ])
            //     ->count();
            // $pppoeonline = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
            //     ->joinSub($sub, 'latest', function ($join) {
            //         $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            //     })
            //     ->where([
            //         ['user_session.shortname', '=', multi_auth()->shortname],
            //         ['user_session.status', '=', 1],
            //         ['user_session.type', '=', 2],
            //         ['user_session.stop', '=', null], // hanya yang belum stop
            //     ])
            //     ->count();

            // // Data invoice dan transaksi
            // $totalunpaid = Invoice::where('shortname', $shortname)->whereYear('due_date', $year)->whereMonth('due_date', $month)->where('status', 'unpaid')->count();
            // $incometoday = Transaksi::where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', $today)->sum('nominal');

            // Data bulanan income & expense
            $dataBulan = [];
            $dataTotalIncome = [];
            $dataTotalExpense = [];
            for ($i = 1; $i <= $month; $i++) {
                $dataBulan[] = Carbon::create()->month($i)->format('F');
                $dataTotalIncome[] = Transaksi::where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->whereNot('created_by', 'frradius')->sum('nominal');
                $dataTotalExpense[] = Transaksi::where('shortname', $shortname)->where('tipe', 'Pengeluaran')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->whereNot('created_by', 'frradius')->sum('nominal');
            }
            $dataIncome = ['labels' => $dataBulan, 'data' => $dataTotalIncome];
            $dataExpense = ['labels' => $dataBulan, 'data' => $dataTotalExpense];

            $activity = ActivityLog::where('shortname', $shortname)->orderBy('id', 'desc')->limit(5)->get();

            if (request()->ajax()) {
                // Untuk Admin, batasi data NAS (limit 5) dan ping-check hanya menampilkan spinner
                return $this->getNasDatatable($shortname, 5, false);
            }

            return view('backend.dashboard.admin_new', compact('activity', 'dataIncome', 'dataExpense'));
        } elseif ($role === 'Teknisi') {
            $hotspotonline = RadiusSession::where('shortname', $shortname)
                ->where([['status', 1], ['type', 1], ['stop', null]])
                ->distinct('username')
                ->count();
            $pppoeonline = RadiusSession::where('shortname', $shortname)
                ->where([['status', 1], ['type', 2], ['stop', null]])
                ->distinct('username')
                ->count();
            $hotspottotal = HotspotUser::where('shortname', $shortname)->count();
            $pppoetotal = PppoeUser::where('shortname', $shortname)->count();

            if (request()->ajax()) {
                // Untuk Teknisi, lakukan ping-check menggunakan RouterOS Client
                return $this->getNasDatatable($shortname, null, true);
            }
            return view('backend.dashboard.teknisi_new', compact('hotspotonline', 'pppoeonline', 'hotspottotal', 'pppoetotal'));
        } elseif ($role === 'Kasir') {
            $hotspotonline = RadiusSession::where('shortname', $shortname)
                ->where([['status', 1], ['type', 1], ['stop', null]])
                ->distinct('username')
                ->count();
            $pppoeonline = RadiusSession::where('shortname', $shortname)
                ->where([['status', 1], ['type', 2], ['stop', null]])
                ->distinct('username')
                ->count();
            $totalunpaid = Invoice::where('shortname', $shortname)->whereYear('due_date', $year)->whereMonth('due_date', $month)->where('status', 'unpaid')->count();
            $totalpaid = Invoice::where('shortname', $shortname)->whereYear('due_date', $year)->whereMonth('due_date', $month)->where('status', 'paid')->count();
            $totaltagihan = Invoice::where('shortname', $shortname)->whereYear('due_date', $year)->whereMonth('due_date', $month)->where('status', 'unpaid')->sum('price');
            $incometoday = Transaksi::where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', $today)->sum('nominal');

            $dataBulan = [];
            $dataTotalIncome = [];
            $dataTotalExpense = [];
            for ($i = 1; $i <= $month; $i++) {
                $dataBulan[] = Carbon::create()->month($i)->format('F');
                $dataTotalIncome[] = Transaksi::where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->whereNot('created_by', 'frradius')->sum('nominal');
                $dataTotalExpense[] = Transaksi::where('shortname', $shortname)->where('tipe', 'Pengeluaran')->whereYear('tanggal', $year)->whereMonth('tanggal', $i)->whereNot('created_by', 'frradius')->sum('nominal');
            }
            $dataIncome = ['labels' => $dataBulan, 'data' => $dataTotalIncome];
            $dataExpense = ['labels' => $dataBulan, 'data' => $dataTotalExpense];

            $activity = ActivityLog::where('shortname', $shortname)->orderBy('id', 'desc')->limit(5)->get();

            if (request()->ajax()) {
                return $this->getNasDatatable($shortname, null, true);
            }

            return view('backend.dashboard.kasir_new', compact('totalunpaid', 'totalpaid', 'totaltagihan', 'incometoday', 'dataIncome', 'dataExpense'));
        } elseif ($role === 'Mitra') {
            $pppoetotal = PppoeUser::where('shortname', $shortname)->where('mitra_id', $auth->id)->count();
            $pppoepending = PppoeUser::where('shortname', $shortname)->where('mitra_id', $auth->id)->where('status', 0)->count();
            $totalkomisi = TransaksiMitra::where('shortname', $shortname)->where('mitra_id', $auth->id)->whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('nominal');
            $totalunpaid = Invoice::where('shortname', $shortname)->where('mitra_id', $auth->id)->where('status', 'unpaid')->whereYear('due_date', $year)->whereMonth('due_date', $month)->count();

            if (request()->ajax()) {
                return $this->getNasDatatable($shortname, null, true);
            }

            return view('backend.dashboard.mitra_new', compact('pppoepending', 'pppoetotal', 'totalkomisi', 'totalunpaid'));
        } elseif ($role === 'Owner') {
            $totaluser = User::where('role', 'Admin')->count();
            $usertrial = User::where('role', 'Admin')->where('license_id', 1)->count();
            $useractive = User::where('role', 'Admin')->whereNot('license_id', 1)->where('status', 1)->count();
            $userexpired = User::where('role', 'Admin')->where('status', 3)->whereNot('license_id', 1)->count();

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

            // Cek status MySQL
            try {
                DB::connection('frradius_auth')->getPdo();
                $mysqlStatus = 'Active';
                $mysqlConnections = DB::connection('frradius_auth')
                    ->select("SHOW STATUS WHERE Variable_name = 'Threads_connected'")[0]->Value;
            } catch (\Exception $e) {
                $mysqlStatus = 'Inactive';
                $mysqlConnections = 'N/A';
            }            

            function isRadiusUdpAlive($port = 1812)
            {
                $host = env('IP_RADIUS_SERVER', '127.0.0.1');
                $port = (int) $port; // <-- Konversi ke integer
            
                $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                if (!$socket) {
                    return false;
                }
            
                $message = "radius-test"; // dummy data
                $sent = socket_sendto($socket, $message, strlen($message), 0, $host, $port);
            
                // Set timeout
                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 1, 'usec' => 0]);
            
                $buf = '';
                $from = '';
                $portOut = 0;
                $recv = @socket_recvfrom($socket, $buf, 512, 0, $from, $portOut);
            
                socket_close($socket);
            
                return $sent !== false;
            }
            
            $radiusStatus = isRadiusUdpAlive('10.29.37.90', 1812) ? 'active' : 'inactive';

            $stat1 = $this->getCpuStat();
            // Tunggu 1 detik agar ada perbedaan waktu
            sleep(1);
            $stat2 = $this->getCpuStat();

            // Hitung total waktu sebelum dan sesudah
            $total1 = array_sum($stat1);
            $total2 = array_sum($stat2);

            // Hitung selisih waktu idle
            $idle1 = 1;// $stat1['idle'];
            $idle2 = 1;//$stat2['idle'];
            if(isset($stat2['idle'])){
                $idle2 = $stat2['idle'];
            }
            if(isset($stat1['idle'])){
                $idle1 = $stat1['idle'];
            }

            $totalDiff = 1; //$total2 - $total1;
            $idleDiff = $idle2 - $idle1;

            // Persentase penggunaan CPU = (totalDiff - idleDiff) / totalDiff * 100
            $cpuUsage = (100 * ($totalDiff - $idleDiff)) / $totalDiff;

            return view('backend.dashboard.owner_new', compact('totaluser', 'usertrial', 'useractive', 'userexpired', 'serverInfo', 'mysqlStatus', 'mysqlConnections', 'radiusStatus', 'cpuUsage'));
        }
    }

    public function data(Request $request)
    {
        $shortname = multi_auth()->shortname;
        $year = now()->year;
        $month = now()->month;
        $today = now()->toDateString();

        // --- Bagian Online Session ---
        $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', $shortname)->groupBy('username');

        $onlineStats = RadiusSession::selectRaw(
            "
                COUNT(DISTINCT CASE WHEN user_session.type = 1 AND user_session.status = 1 AND user_session.stop IS NULL THEN user_session.username END) as hs_online,
                COUNT(DISTINCT CASE WHEN user_session.type = 2 AND user_session.status = 1 AND user_session.stop IS NULL THEN user_session.username END) as pppoe_online
            ",
        )
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            })
            ->where('user_session.shortname', $shortname)
            ->first();

        $hotspotonline = $onlineStats ? $onlineStats->hs_online : 0;
        $pppoeonline = $onlineStats ? $onlineStats->pppoe_online : 0;

        // --- Invoice & Transaksi ---
        $totalunpaid = Invoice::where('shortname', $shortname)->whereYear('due_date', $year)->whereMonth('due_date', $month)->where('status', 'unpaid')->count();
        $incometoday = Transaksi::where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereDate('tanggal', $today)->sum('nominal');

        // --- Data Income & Expense (optimasi groupBy) ---
        $incomeGroup = Transaksi::selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')->where('shortname', $shortname)->where('tipe', 'Pemasukan')->whereYear('tanggal', $year)->whereNot('created_by', 'frradius')->groupBy('bulan')->pluck('total', 'bulan');

        $expenseGroup = Transaksi::selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')->where('shortname', $shortname)->where('tipe', 'Pengeluaran')->whereYear('tanggal', $year)->whereNot('created_by', 'frradius')->groupBy('bulan')->pluck('total', 'bulan');

        $dataBulan = [];
        $dataTotalIncome = [];
        $dataTotalExpense = [];
        for ($i = 1; $i <= $month; $i++) {
            $dataBulan[] = Carbon::create()->month($i)->format('F');
            $dataTotalIncome[] = $incomeGroup->has($i) ? $incomeGroup[$i] : 0;
            $dataTotalExpense[] = $expenseGroup->has($i) ? $expenseGroup[$i] : 0;
        }
        $dataIncome = ['labels' => $dataBulan, 'data' => $dataTotalIncome];
        $dataExpense = ['labels' => $dataBulan, 'data' => $dataTotalExpense];

        // --- Activity Log ---
        $activity = ActivityLog::where('shortname', $shortname)->orderBy('id', 'desc')->limit(5)->get();

        return response()->json([
            'incometoday' => $incometoday,
            'totalunpaid' => $totalunpaid,
            'hotspotonline' => $hotspotonline,
            'pppoeonline' => $pppoeonline,
            'dataIncome' => $dataIncome,
            'dataExpense' => $dataExpense,
            'activity' => $activity,
        ]);
    }

    // Fungsi untuk mendapatkan statistik CPU dari /proc/stat
    private function getCpuStat()
    {
        $stats = [];
        // $lines = file('/proc/stat');
        if (file_exists('/proc/stat')) {
            $lines = file('/proc/stat');
        } else {
            $lines = null; // atau fallback lain
        }
        if (isset($lines[0])) {
            // Baris pertama berisi data CPU secara keseluruhan: "cpu  123 456 789 012 ..."
            $parts = preg_split('/\s+/', trim($lines[0]));
            // parts[0] adalah "cpu"
            $stats['user'] = (int) $parts[1];
            $stats['nice'] = (int) $parts[2];
            $stats['system'] = (int) $parts[3];
            $stats['idle'] = (int) $parts[4];
            $stats['iowait'] = isset($parts[5]) ? (int) $parts[5] : 0;
            $stats['irq'] = isset($parts[6]) ? (int) $parts[6] : 0;
            $stats['softirq'] = isset($parts[7]) ? (int) $parts[7] : 0;
            $stats['steal'] = isset($parts[8]) ? (int) $parts[8] : 0;
        }
        return $stats;
    }

    /**
     * Membuat data DataTables untuk data NAS.
     *
     * @param string   $shortname
     * @param int|null $limit
     * @param bool     $withPing  Jika true, lakukan ping-check menggunakan RouterOS Client
     * @return \Illuminate\Http\JsonResponse
     */
    private function getNasDatatable($shortname, $limit = null, $withPing = false)
    {
        // $query = Nas::query()
        //     ->selectRaw('radius_mikrotik.*, count(distinct user_session.username) as unique_ronline')
        //     ->leftJoin('user_session', function ($join) {
        //         $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')->where('user_session.status', 1)->whereNull('user_session.stop');
        //     })
        //     ->where('radius_mikrotik.shortname', $shortname)
        //     ->groupBy('radius_mikrotik.id');

        // $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', multi_auth()->shortname)->groupBy('username');
        // $query = Nas::query()->where('radius_mikrotik.shortname',multi_auth()->shortname)
        // ->selectRaw('radius_mikrotik.*, COUNT(user_session.id) as online_count')
        // ->leftJoin('user_session', function ($join) use ($sub) {
        //     $join->on('radius_mikrotik.ip_router', '=', 'user_session.nas_address')
        //          ->joinSub($sub, 'latest', function ($joinSub) {
        //              $joinSub->on('user_session.username', '=', 'latest.username')
        //                      ->on('user_session.start', '=', 'latest.latest_start');
        //          })
        //          ->where('user_session.shortname', '=', multi_auth()->shortname)
        //          ->where('user_session.status', '=', 1)
        //          ->whereNull('user_session.stop');
        // })
        // ->groupBy('radius_mikrotik.id');

        $query = Nas::query()->where('shortname', multi_auth()->shortname);

        if ($limit) {
            $query->limit($limit);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('total_session', function ($row) {
                return '0';
            })
            ->addColumn('ping', function ($row) use ($withPing) {
                if (!$withPing) {
                    return '<span data-id="' . $row->id . '" class="ping-check material-symbols-outlined spinner">progress_activity</span>';
                }
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
                return $response ? 1 : 0;
            })
            ->addColumn('action', function ($row) {
                return '<a href="javascript:void(0)" id="show" data-id="' .
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
            ->rawColumns(['ping', 'action'])
            ->toJson();
    }
}
