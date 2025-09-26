<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Nas;
use App\Models\RadiusNas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\License;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class NasMikrotikController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $radii = Nas::query()->where('group_id', $request->user()->id_group);
            return DataTables::of($radii)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="show"
                    data-id="' . $row->id . '" class="badge badge-primary">
                        <i class="fas fa-copy"></i>&nbsp;Show Script
                    </a>
                    <a href="javascript:void(0)"
                    class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                    <i class="fas fa-trash-alt"></i>
                    </a>';
                })
                ->toJson();
        }

        // Retrieve IP addresses from config (or directly from env if preferred)
        $vpnIp = config('services.radius.vpn');
        $publicIp = config('services.radius.public');

        // Pass variables to the Blade view
        return view('settings.mikrotik.nas.index', compact('vpnIp', 'publicIp'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:db_profile.nas',
            'ip_router' => 'required|string|min:3|max:255|unique:db_profile.nas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $license = License::where('id', $request->user()->license_id)->first();
        $existingNas = Nas::where('group_id', $request->user()->id_group)->count();

        if ($existingNas >= $license->limit_nas) {
            return response(400)->json([
                'message' => 'Limit NAS telah tercapai, silahkan upgrade lisensi anda',
            ]);
        }

        $nas = Nas::create([
            'group_id' => $request->user()->id_group,
            'name' => $request->name,
            'ip_router' => $request->ip_router,
            'secret' => str()->random(16),
            'timezone' => $request->timezone,
        ]);

        $rnas = RadiusNas::create([
            'group_id' => $request->user()->id_group,
            'shortname' => $request->user()->shortname,
            'nasname' => $request->ip_router,
            'secret' => $nas->secret,
            'timezone' => $request->timezone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $nas,
            $rnas,
        ]);
    }

    public function show($id)
    {
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
        $nas_ipArray = Nas::where('id', $id)->select('ip_router')->get();
        $rnas_ip = $nas_ipArray[0]['ip_router'];
        RadiusNas::where('nasname', $rnas_ip)->delete();
        $nas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
