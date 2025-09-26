<?php

namespace App\Http\Controllers\Mapping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapping\Pop;
use App\Models\Mapping\Odp;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Pppoe\PppoeUser;

class OdpController extends Controller
{
    public function index()
    {
        $areas = Pop::where('shortname', multi_auth()->shortname)
            ->orderBy('kode_area', 'asc')
            ->get();

        if (request()->ajax()) {
            $odps = Odp::query()
                ->where('shortname', multi_auth()->shortname)
                ->with('area')
                ->orderBy('id', 'desc');

            return DataTables::of($odps)
                ->addIndexColumn()
                ->editColumn('area.kode_area', function ($odps) {
                    return $odps->area->kode_area;
                })
                ->addColumn('jml_plgn', function ($row) {
                    return $row->hasMany(PppoeUser::class, 'kode_odp', 'kode_odp')
                        ->where('shortname', multi_auth()->shortname)
                        ->count();
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="https://www.google.com/maps?q=' . $row->latitude . ',' . $row->longitude . '" target="_blank" data-id="' .
                        $row->id . '" class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-map"></i>
                    </a>
                    <a href="javascript:void(0)" id="edit"
                        data-id="' . $row->id . '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="javascript:void(0)" id="delete"
                        data-id="' . $row->id . '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-trash"></i>
                    </a>';
                })
                ->toJson();
        }

        return view('backend.mapping.odp.index_new', compact('areas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_odp' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('mapping_odp')->where('shortname', multi_auth()->shortname),
            ],
            'port_odp' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $odp = Odp::create([
            'shortname' => multi_auth()->shortname,
            'kode_odp' => $request->kode_odp,
            'deskripsi' => $request->deskripsi,
            'port_odp' => $request->port_odp,
            'kode_area_id' => $request->kode_area_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $odp,
        ]);
    }

    public function show(Odp $odp)
    {
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
        $odp->update([
            'kode_odp' => $request->kode_odp,
            'port_odp' => $request->port_odp,
            'deskripsi' => $request->deskripsi,
            'kode_area_id' => $request->kode_area_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
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
}
