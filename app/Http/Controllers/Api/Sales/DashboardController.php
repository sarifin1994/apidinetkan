<?php


namespace App\Http\Controllers\Api\Sales;


use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Pppoe\PppoeUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){

        $auth = $request->user();
        $role = $auth->role;
        $shortname = $auth->shortname;
        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;
        $pppoetotal = PppoeUser::where('shortname', $shortname)->where('mitra_id', $auth->id)->count();
        $pppoepending = PppoeUser::where('shortname', $shortname)->where('mitra_id', $auth->id)->where('status', 0)->count();
        $totalkomisi = TransaksiMitra::where('shortname', $shortname)->where('mitra_id', $auth->id)->whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('nominal');
        $totalunpaid = Invoice::where('shortname', $shortname)->where('mitra_id', $auth->id)->where('status', 'unpaid')->whereYear('due_date', $year)->whereMonth('due_date', $month)->count();

        return response()->json(
            array(
                'pppoetotal' => $pppoetotal,
                'pppoepending' => $pppoepending,
                'totalkomisi' => $totalkomisi,
                'totalunpaid' => $totalunpaid
            )
        );

    }
}
