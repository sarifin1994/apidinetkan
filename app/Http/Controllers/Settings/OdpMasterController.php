<?php

namespace App\Http\Controllers\Settings;

use App\Models\Odp;
use App\Models\Area;
use App\Models\PppoeUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class OdpMasterController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::where('group_id', $request->user()->id_group)->get();
        if (request()->ajax()) {
            $odps = Odp::query()->where('group_id', $request->user()->id_group)->with('area')->orderBy('id', 'desc');
            return DataTables::of($odps)
                ->addIndexColumn()
                ->editColumn('area.kode_area', function ($odps) {
                    return $odps->area->kode_area;
                })
                ->editColumn('jml_plgn', function ($row) use ($request) {
                    return $row->hasMany(PppoeUser::class, 'kode_odp', 'kode_odp')->where('group_id', $request->user()->id_group)->count();
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                        $row->id .
                        '" class="badge b-ln-height badge-primary">
                        <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                <i class="fas fa-trash-alt"></i>
                </a>';
                })
                ->toJson();
        }

        $total = Odp::where('group_id', $request->user()->id_group)->count();

        return view('settings.master.odp.index', compact('areas', 'total'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_area_id' => 'required',
            'kode_odp' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('db_ftth.master_odp')->where('group_id', $request->user()->id_group),
            ],
            'port_odp' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $odp = Odp::create([
            'group_id' => $request->user()->id_group,
            'kode_odp' => $request->kode_odp,
            'port_odp' => $request->port_odp,
            'kode_area_id' => $request->kode_area_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $odp,
        ]);
    }

    public function show(Odp $odp)
    {
        //return response
        $data = Odp::where('kode_area_id', $odp->kode_area_id)
            ->with('area')
            ->find($odp->id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $data,
        ]);
    }
    public function update(Request $request, Odp $odp)
    {
        // $validator = Validator::make($request->all(), [
        //     'port_odp' => 'required|integer|min:1',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        $odp->update([
            'kode_odp' => $request->kode_odp,
            'port_odp' => $request->port_odp,
            'kode_area_id' => $request->kode_area_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $odp,
        ]);
    }
    public function destroy($id)
    {
        $odp = Odp::findOrFail($id);
        $odp->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function listOptions(Request $request)
    {
        $odps = Odp::query()
            ->where('group_id', $request->user()->id_group)
            ->get(['id', 'kode_odp']);

        return response()->json($odps);
    }
}
