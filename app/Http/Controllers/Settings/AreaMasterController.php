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

class AreaMasterController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $areas = Area::query()
                ->where('group_id', $request->user()->id_group)
                ->orderBy('id', 'desc');
            return DataTables::of($areas)
                ->addIndexColumn()
                ->editColumn('jml_odp', function ($row) use ($request) {
                    return $row
                        ->hasMany(Odp::class, 'kode_area_id', 'id')
                        ->where('group_id', $request->user()->id_group)
                        ->count();
                })
                ->editColumn('jml_plgn', function ($row) use ($request) {
                    return $row
                        ->hasMany(PppoeUser::class, 'kode_area', 'kode_area')
                        ->where('group_id', $request->user()->id_group)
                        ->count();
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                        $row->id .
                        '" class="badge b-ln-height badge-primary">
                        <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' .
                        $row->id .
                        '">
                <i class="fas fa-trash-alt"></i>
                </a>';
                })
                ->toJson();
        }

        $total = Area::where('group_id', $request->user()->id_group)->count();

        return view('settings.master.area.index', compact('total'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_area' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('db_ftth.master_area')->where('group_id', $request->user()->id_group),
            ],
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $area = Area::create([
            'group_id' => $request->user()->id_group,
            'kode_area' => $request->kode_area,
            'deskripsi' => $request->deskripsi,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $area,
        ]);
    }

    public function show(Area $area)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $area,
        ]);
    }

    public function update(Request $request, Area $area)
    {
        $validator = Validator::make($request->all(), [
            // 'kode_area' => ['required', 'string','min:2','max:255', 'unique:'.Area::class],
            'kode_area' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $area->update([
            'kode_area' => $request->kode_area,
            'deskripsi' => $request->deskripsi,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $area,
        ]);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function listOptions(Request $request)
    {
        $areas = Area::query()
            ->where('group_id', $request->user()->id_group)
            ->get(['id', 'kode_area']);

        return response()->json($areas);
    }
}
