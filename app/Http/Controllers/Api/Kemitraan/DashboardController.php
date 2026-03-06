<?php


namespace App\Http\Controllers\Api\Kemitraan;


use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Hotspot\HotspotUser;
use App\Models\Invoice\Invoice;
use App\Models\Keuangan\Transaksi;
use App\Models\License;
use App\Models\MappingUserLicense;
use App\Models\Mikrotik\Nas;
use App\Models\Pppoe\PppoeUser;
use App\Models\Radius\RadiusSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\ServiceStatusEnum;

class DashboardController extends Controller
{
    public function index(Request $request){
        $shortname = $request->user()->shortname;
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

        $pppoeuser = PppoeUser::where('shortname', $request->user()->shortname)->count();
        $pppoeuserlimit = \App\Models\Owner\License::find($request->user()->license_id)->limit_pppoe;

        $hsuser = HotspotUser::where('shortname', $request->user()->shortname)->count();
        $hsuserlimit = \App\Models\Owner\License::find($request->user()->license_id)->limit_hs;

        $nas = $this->getNasDatatable($request, 5, false);
        return response()->json([
            'income_today' => $incometoday,
            'total_unpaid' => $totalunpaid,
            'hotspot_online' => $hotspotonline,
            'pppoe_online' => $pppoeonline,
            'data_income' => $dataIncome,
            'data_expense' => $dataExpense,
            'activity' => $activity,
            'radius_engine' => 'running',
            'license' => License::query()->find($request->user()->license_id),
            'due_date' => $request->user()->next_due,
            'pppoe_user' => $pppoeuser.'/'.$pppoeuserlimit,
            'hotspot_user' => $hsuser.'/'.$hsuserlimit,
            'mikrotik' => $nas
        ]);
    }

    private function getNasDatatable(Request $request, $limit = null, $withPing = false)
    {
        $query = Nas::query()->where('shortname', $request->user()->shortname);

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
            ->rawColumns(['ping'])
            ->toJson();
    }


    public function informasi_layanan(Request $request){
        $services = MappingUserLicense::query()
            ->where('dinetkan_user_id', $request->user()->dinetkan_user_id)
            ->where('status', ServiceStatusEnum::ACTIVE->value)
            ->with('service')
            ->with('service_detail')
            ->with('service_libre')
            ->orderBy('created_at', 'asc')
            ->get();

        $services = $services->map(function($r){
            return [
                'service_id' => $r->service_id,
                'status' => ServiceStatusEnum::ACTIVE->label(),
               'bandwith' => $r->Service->capacity,
               'expired' => $r->due_date,
               'ip_public' => $r->service_detail->ip_prefix
           ];
        });

        return response()->json($services);
    }
}
