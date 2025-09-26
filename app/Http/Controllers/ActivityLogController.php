<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
   
    public function index()
    {
        if (request()->ajax()) {
            $log = ActivityLog::query()->where('shortname', multi_auth()->shortname)->with('causer');
            return DataTables::of($log)
                ->addIndexColumn()
                ->editColumn('username', function ($row) {
                    return optional($row->causer)->username ?? (optional($row->causer)->name ?? '-');
                })
                ->editColumn('role', function ($row) {
                    return optional($row->causer)->role ?? '-';
                })
                ->toJson();
        }
        return view('backend.log.index_new');
    }
}
