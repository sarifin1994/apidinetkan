<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Models\RadiusSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class OnlineController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            try {
                // Subquery to get the latest session ID for each username
                $sub = RadiusSession::select('username', DB::raw('MAX(id) as latest_id'))
                    ->where('shortname', $request->user()->shortname)
                    ->where([['status', 1], ['type', 2]])
                    ->groupBy('username');

                // Join the subquery to get the latest session details per username
                $online = RadiusSession::joinSub($sub, 'latest_sessions', function ($join) {
                    $join->on('user_session.id', '=', 'latest_sessions.latest_id');
                })
                    ->select([
                        'user_session.id',
                        'user_session.session_id',
                        'user_session.username',
                        'user_session.ip',
                        'user_session.mac',
                        'user_session.input',
                        'user_session.output',
                        'user_session.uptime',
                        'user_session.start'
                    ])
                    ->orderBy('user_session.id', 'desc');

                return DataTables::of($online)
                    ->addIndexColumn()
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTables Error: ' . $e->getMessage());
                return response()->json(['error' => 'An error occurred while processing your request.'], 500);
            }
        }

        return view('pppoe.online.index');
    }
}
