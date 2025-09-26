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

class AreaController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $areas = Pop::query()
                ->where('shortname', multi_auth()->shortname)
                ->orderBy('id', 'desc');

            return DataTables::of($areas)
                ->addIndexColumn()
                ->addColumn('jml_odp', function ($row) {
                    return $row->hasMany(Odp::class, 'kode_area_id', 'id')
                        ->where('shortname', multi_auth()->shortname)
                        ->count();
                })
                ->addColumn('jml_plgn', function ($row) {
                    return $row->hasMany(PppoeUser::class, 'kode_area', 'kode_area')
                        ->where('shortname', multi_auth()->shortname)
                        ->count();
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                        data-id="' . $row->id . '" class="btn btn-secondary text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="javascript:void(0)" id="delete"
                        data-id="' . $row->id . '" class="btn btn-danger text-white"
                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <i class="ti ti-trash"></i>
                    </a>';
                })
                ->toJson();
        }

        return view('backend.mapping.area.index_new');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_area' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('mapping_area')->where('shortname', multi_auth()->shortname),
            ],
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $area = Pop::create([
            'shortname' => multi_auth()->shortname,
            'kode_area' => $request->kode_area,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $area,
        ]);
    }

    public function show(Pop $pop)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $pop,
        ]);
    }

    public function update(Request $request, Pop $pop)
    {
        $validator = Validator::make($request->all(), [
            'kode_area' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $pop->update([
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $pop,
        ]);
    }

    public function destroy($id)
    {
        $area = Pop::findOrFail($id);
        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
