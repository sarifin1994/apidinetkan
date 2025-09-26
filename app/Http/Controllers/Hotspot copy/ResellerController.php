<?php

namespace App\Http\Controllers\Hotspot;

use App\Models\Nas;
use App\Models\Area;
use App\Models\User;
use App\Models\HotspotUser;
use Illuminate\Http\Request;
use App\Models\HotspotProfile;
use App\Models\HotspotReseller;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\HotspotTransaksiReseller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::where('group_id', $request->user()->id_group)->get();
        $nas = Nas::where('group_id', $request->user()->id_group)->get();
        $profiles = HotspotProfile::where('group_id', $request->user()->id_group)->get();
        if (request()->ajax()) {
            $reseller = HotspotReseller::query()->where('group_id', $request->user()->id_group);
            return DataTables::of($reseller)
                ->addIndexColumn()
                ->editColumn('voucher_new', function ($row) use ($request) {
                    return $row->hasMany(HotspotUser::class, 'reseller_id', 'id')->where('group_id', $request->user()->id_group)->where('status', 1)->count();
                })
                ->editColumn('voucher_aktif', function ($row) use ($request) {
                    return $row->hasMany(HotspotUser::class, 'reseller_id', 'id')->where('group_id', $request->user()->id_group)->where('status', 2)->count();
                })
                ->editColumn('login', function ($row) use ($request) {
                    return $row->hasMany(User::class, 'reseller_id', 'id')->where('id_group', $request->user()->id_group)->count();
                })
                ->addColumn('action', function ($row) {
                    if ($row->status === 0) {
                        return '<a href="javascript:void(0)" id="edit"
                        data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-primary">
                            <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0)" id="deposit"
                        data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-secondary">
                            <i class="fas fa-bank"></i>
                    </a>
                    <a href="javascript:void(0)" id="enable"
                        data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-success">
                            <i class="fas fa-user-check"></i>
                    </a>';
                    } else {
                        return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-primary">
                        <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)" id="deposit"
                        data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-secondary">
                            <i class="fas fa-bank"></i>
                    </a>
                <a href="javascript:void(0)" id="disable"
                    data-id="' .
                            $row->id .
                            '" class="badge b-ln-height badge-danger">
                        <i class="fas fa-user-slash"></i>
                </a>';
                    }
                    // <a href="javascript:void(0)"
                    // class="badge b-ln-height badge-danger" id="delete" data-id="' .
                    //         $row->id .
                    //         '">
                    // <i class="fas fa-trash-alt"></i>
                    // </a>
                })
                ->toJson();
        }

        $count = HotspotReseller::where('group_id', $request->user()->id_group)->count();

        return view('hotspot.reseller.index', compact('areas', 'nas', 'profiles', 'count'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('db_profile.reseller')->where('group_id', $request->user()->id_group),
            ],
            'wa' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $reseller = HotspotReseller::create([
            'group_id' => $request->user()->id_group,
            'name' => $request->name,
            'wa' => $request->wa,
            'kode_area' => $request->kode_area,
            'nas' => $request->nas,
            'profile' => json_encode($request->profile),
            'status' => 1,
            'cetak' => $request->cetak,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $reseller,
        ]);
    }

    public function show(HotspotReseller $reseller)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $reseller,
        ]);
    }

    public function update(Request $request, HotspotReseller $reseller)
    {
        $validator = Validator::make($request->all(), [
            'name_edit' => 'required',
            'wa_edit' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $reseller->update([
            'name' => $request->name_edit,
            'wa' => $request->wa_edit,
            'kode_area' => $request->kode_area_edit,
            'nas' => $request->nas_edit,
            'profile' => json_encode($request->profile_edit),
            'cetak' => $request->cetak_edit,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $reseller,
        ]);
    }

    public function disable(Request $request)
    {
        $reseller = HotspotReseller::where('id', $request->id);
        $reseller->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reseller Berhasil Dinonaktifkan',
            'data' => $reseller,
        ]);
    }

    public function enable(Request $request)
    {
        $reseller = HotspotReseller::where('id', $request->id);
        $reseller->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reseller Berhasil Diaktifkan',
            'data' => $reseller,
        ]);
    }

    public function deposit(Request $request)
    {
        $reseller = HotspotReseller::where('id', $request->reseller_id)->select('balance', 'name')->first();
        $transaksi = HotspotTransaksiReseller::create([
            'group_id' => $request->group_id,
            'reseller_id' => $request->reseller_id,
            'type' => '1',
            'item' => 'Deposit Reseller a.n ' . $reseller->name,
            'nominal' => str_replace('.', '', $request->jml_deposit),
        ]);
        $balance = HotspotReseller::where('id', $request->reseller_id)->update([
            'balance' => $reseller->balance + str_replace('.', '', $request->jml_deposit),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Deposit Berhasil',
        ]);
    }

    public function destroy($id)
    {
        $reseller = HotspotReseller::findOrFail($id);
        $reseller->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
