<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PppoeUser;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OfflineController extends Controller
{
    public function index(Request $request)
    {
        $groupId = $request->user()->id_group;

        if ($request->ajax()) {
            // Step 1: Subquery for each user’s most recent session
            $latestSessionSub = DB::table('db_radius.user_session as us')
                ->select('us.username', 'us.session_id', 'us.ip', 'us.status', 'us.stop', 'us.update')
                ->join(
                    DB::raw('(SELECT username, MAX(`update`) AS max_update
                              FROM user_session
                              GROUP BY username) AS x'),
                    function ($join) {
                        $join->on('us.username', '=', 'x.username')
                             ->on('us.update', '=', 'x.max_update');
                    }
                );

            // Step 2: Join the subquery & filter “offline” users
            $users = PppoeUser::query()
                ->select(
                    'user_pppoe.*',
                    'ls.session_id as last_session_id',
                    'ls.ip as last_session_ip',
                    'ls.status as last_session_status',
                    'ls.stop as last_session_stop',
                    'ls.update as last_session_update'
                )
                ->leftJoinSub($latestSessionSub, 'ls', function ($join) {
                    $join->on('user_pppoe.username', '=', 'ls.username');
                })
                ->with([
                    'member:id_service,pppoe_id,member_id,payment_type,billing_period,reg_date,next_due',
                    'member.data:id,full_name',
                    'radius:name,ip_router',
                    'rprofile:id,name',
                    'rarea:kode_area,id',
                    'rodp:id,kode_odp',
                ])
                ->where('user_pppoe.group_id', $groupId)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('ls.status', 2)
                          ->whereNotNull('ls.stop');
                    })
                    ->orWhereNull('ls.username');
                });

            // Step 3: DataTables as normal
            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('nas_name', fn($row) => $row->radius->name ?? '-')
                ->editColumn('profile_name', fn($row) => $row->rprofile->name ?? '-')
                ->editColumn('area_name', fn($row) => $row->rarea->kode_area ?? '-')
                ->editColumn('odp_name', fn($row) => $row->rodp->kode_odp ?? '-')
                ->editColumn('session_internet', fn($row) => $row->last_session_id ?? '-')
                ->toJson();
        }

        return view('pppoe.offline.index');
    }
}
