<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $log = ActivityLog::query()->where('group_id', $request->user()->id_group)->with('user');
            return DataTables::of($log)
                ->addIndexColumn()
                ->editColumn('username', function ($row) {
                    return $row->user->username;
                })
                ->editColumn('role', function ($row) {
                    return $row->user->role;
                })
                ->toJson();
        }

        return view('settings.activity.index');
    }
}
