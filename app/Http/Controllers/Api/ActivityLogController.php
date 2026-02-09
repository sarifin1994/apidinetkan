<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ActivityLog::with('causer')
            ->where('shortname', $user->shortname)
            ->orderByDesc('created_at');

        // optional filter
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $logs = $query->get();

        $data = $logs->map(function ($row) {
            return [
                'id' => $row->id,
                'event' => $row->event,
                'description' => $row->description,
                'username' => optional($row->causer)->username
                    ?? optional($row->causer)->name
                    ?? '-',
                'role' => optional($row->causer)->role ?? '-',
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($data);
    }
}
