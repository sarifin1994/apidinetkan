<?php

namespace App\Http\Controllers\Mikrotik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Mikrotik\Nas;
use App\Models\Radius\RadiusNas;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Mikrotik\Vpn;
use App\Models\Radius\RadiusSession;

class NasController extends Controller
{
    public function index()
    {
        $vpns = Vpn::where('shortname', multi_auth()->shortname)->get();
        if (request()->ajax()) {
            $nas = Nas::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($nas)
                ->addIndexColumn()
                ->addColumn('total_session', function ($row) {
                    return '0';
                })
                ->addColumn('ping', function ($row) {
                    return '<span data-id="' . $row->id . '" class="ping-check material-symbols-outlined spinner">progress_activity</span>'; // atau 'Loading...'
                })
                ->rawColumns(['ping', 'action'])
                ->addColumn('action', function ($row) {
                    return '
        <div class="btn-group" role="group">
            <a href="javascript:void(0)" id="show" data-id="' . $row->id . '" 
               class="btn btn-sm btn-primary text-white" title="Lihat Script">
                <i class="ti ti-code-dots"></i> Script
            </a>
            <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" 
               class="btn btn-sm btn-danger text-white" title="Hapus">
                <i class="ti ti-trash"></i>
            </a>
        </div>
    ';
                })

                ->toJson();
        }
        return view('backend.radius.mikrotik.index_new', compact('vpns'));
    }

    public function checkPing(Request $request)
    {
        $nas = Nas::find($request->id);

        if (!$nas) {
            return response()->json(['ping' => false]);
        }

        try {
            $client = new Client([
                'host' => $nas->ip_router,
                'user' => $nas->user,
                'pass' => $nas->password,
                'port' => $nas->port_api,
            ]);
            $query = new Query('/system/identity/print');
            $response = $client->query($query)->read();

            return response()->json(['ping' => $response ? true : false]);
        } catch (\Exception $e) {
            return response()->json(['ping' => false]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_router' => 'required|string|min:3|max:255|unique:radius_mikrotik',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $user = 'FRAuth' . rand(111, 999);
        $password = str()->random(5);

        $nas = Nas::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->name,
            'ip_router' => $request->ip_router,
            'secret' => 'radiusqu',
            'port_api' => $request->port_api,
            'timezone' => $request->timezone,
            'user' => $user,
            'password' => $password,
        ]);
        $rnas = RadiusNas::create([
            'shortname' => multi_auth()->shortname,
            'nasname' => $request->ip_router,
            'secret' => 'radiusqu',
            'timezone' => $request->timezone,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $nas,
            $rnas,
        ]);
    }

    public function show($id)
    {
        //return response
        $radius = Nas::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $radius,
        ]);
    }

    public function destroy($id)
    {
        $nas = Nas::findOrFail($id);
        $nasip = Nas::where('id', $id)->select('ip_router')->first();
        $rnas = RadiusNas::where('nasname', $nasip->ip_router)->delete();
        $nas->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function getTotalSession(Request $request)
    {
        $ip_router = Nas::where('id', $request->id)->select('ip_router')->first()->ip_router;

        $sub = RadiusSession::selectRaw('MAX(start) as latest_start, username')->where('shortname', multi_auth()->shortname)->where('nas_address', $ip_router)->groupBy('username');
        $online = RadiusSession::select(['user_session.session_id', 'user_session.username', 'user_session.ip', 'user_session.mac', 'user_session.input', 'user_session.output', 'user_session.uptime', 'user_session.start', 'user_session.stop', 'user_session.nas_address'])
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('user_session.username', '=', 'latest.username')->on('user_session.start', '=', 'latest.latest_start');
            })
            ->where([
                ['user_session.shortname', '=', multi_auth()->shortname],
                ['user_session.status', '=', 1],
                // ['user_session.type', '=', 1],
                ['user_session.stop', '=', null], // hanya yang belum stop
            ])
            ->with('mnas', 'ppp:username,full_name,kode_area,kode_odp');

        // Menghitung total sesi yang diambil berdasarkan join subquery di atas
        $total_session = $online->count();

        return response()->json([
            'total_session' => $total_session,
        ]);
    }
}
