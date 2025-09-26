<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partnership\Reseller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Hotspot\HotspotUser;

use App\Models\Hotspot\HotspotProfile;

class ResellerController extends Controller
{
    public function index()
    {
        $profiles = HotspotProfile::where('shortname', multi_auth()->shortname)->get();
        if (request()->ajax()) {
            $resellers = Reseller::query()
                ->where('shortname', multi_auth()->shortname)
                ->orderBy('id', 'desc');
            return DataTables::of($resellers)
                ->addIndexColumn()
                ->addColumn('jml_plgn', function ($row) {
                    return $row
                        ->hasMany(HotspotUser::class, 'reseller_id', 'id')
                        ->where('shortname', multi_auth()->shortname)
                        ->where('status', 1)
                        ->count();
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '
        <a href="javascript:void(0)" id="edit" data-id="' . $row->id . '"
            class="btn btn-secondary btn-sm text-white me-1" title="Edit">
            <i class="ti ti-edit"></i>
        </a>';

                    if ($row->status === 1) {
                        $disableBtn = '
            <a href="javascript:void(0)" id="disable" data-id="' . $row->id . '"
                class="btn btn-primary btn-sm text-white me-1" title="Nonaktifkan">
                <i class="ti ti-user-off"></i>
            </a>';
                        return $editBtn . $disableBtn;
                    } else {
                        $enableBtn = '
            <a href="javascript:void(0)" id="enable" data-id="' . $row->id . '"
                class="btn btn-primary btn-sm text-white me-1" title="Aktifkan">
                <i class="ti ti-user-check"></i>
            </a>';
                        return $editBtn . $enableBtn;
                    }
                })

                ->toJson();
        }
        return view('backend.partnership.reseller.index_new', compact('profiles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_reseller' => ['required', 'string', 'min:5', 'max:10', Rule::unique('partnership_reseller')->where('shortname', multi_auth()->shortname)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $reseller = Reseller::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->nama_reseller,
            'id_reseller' => $request->id_reseller,
            'password' => Hash::make($request->password),
            'login' => $request->login,
            'cetak' => $request->cetak,
            'billing' => $request->billing,
            'nomor_wa' => $request->nomor_wa,
            'profile' => json_encode($request->profile),
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $reseller,
        ]);
    }

    public function show(Reseller $reseller)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $reseller,
        ]);
    }

    public function update(Request $request, Reseller $reseller)
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'nomor_wa' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        if ($request->password == NULL) {
            $reseller->update([
                'name' => $request->nama_reseller,
                'id_reseller' => $request->id_reseller,
                'login' => $request->login,
                'cetak' => $request->cetak,
                'nomor_wa' => $request->nomor_wa,
                'profile' => json_encode($request->profile),
            ]);
        } elseif ($request->profile == NULL) {
            $reseller->update([
                'name' => $request->nama_reseller,
                'id_reseller' => $request->id_reseller,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'cetak' => $request->cetak,
                'nomor_wa' => $request->nomor_wa,
            ]);
        } elseif ($request->password == NULL && $request->profile == NULL) {
            $reseller->update([
                'name' => $request->nama_reseller,
                'id_reseller' => $request->id_reseller,
                'login' => $request->login,
                'cetak' => $request->cetak,
                'nomor_wa' => $request->nomor_wa,
            ]);
        } else {
            $reseller->update([
                'name' => $request->nama_reseller,
                'id_reseller' => $request->id_reseller,
                'password' => Hash::make($request->password),
                'login' => $request->login,
                'cetak' => $request->cetak,
                'nomor_wa' => $request->nomor_wa,
                'profile' => json_encode($request->profile),
            ]);
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $reseller,
        ]);
    }

    public function destroy($id)
    {
        $reseller = Reseller::findOrFail($id);
        $reseller->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $reseller = Reseller::where('id', $request->id);
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
        $reseller = Reseller::where('id', $request->id);
        $reseller->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reseller Berhasil Diaktifkan',
            'data' => $reseller,
        ]);
    }
}
