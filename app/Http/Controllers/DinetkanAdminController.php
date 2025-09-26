<?php


namespace App\Http\Controllers;


use App\Models\DinetkanAdmin;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\Partnership\Mitra;
use App\Models\Pppoe\PppoeProfile;
use App\Models\Pppoe\PppoeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DinetkanAdminController
{
    public function index()
    {
        if (request()->ajax()) {
            $mitras = DinetkanAdmin::query()
                ->where('shortname', multi_auth()->shortname)
                ->orderBy('id', 'desc');
            return DataTables::of($mitras)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editBtn = '
                                <a href="javascript:void(0)" id="edit" data-id="' . $row->id . '"
                                    class="btn btn-secondary btn-sm text-white me-1">
                                    <i class="ti ti-edit"></i>
                                </a>';

                                            $deleteBtn = '
                                <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '"
                                    class="btn btn-danger btn-sm text-white me-1">
                                    <i class="ti ti-trash"></i>
                                </a>';

                    if ($row->status === 1) {
                        $disableBtn = '
                            <a href="javascript:void(0)" id="disable" data-id="' . $row->id . '"
                                class="btn btn-primary btn-sm text-white me-1">
                                <i class="ti ti-user-off"></i>
                            </a>';
                        return $editBtn . $disableBtn . $deleteBtn;
                    } else {
                        $enableBtn = '
                            <a href="javascript:void(0)" id="enable" data-id="' . $row->id . '"
                                class="btn btn-primary btn-sm text-white me-1">
                                <i class="ti ti-user-check"></i>
                            </a>';
                        return $editBtn . $enableBtn . $deleteBtn;
                    }
                })

                ->toJson();
        }
        return view('backend.dinetkan_sales.index_new');
    }

    public function store(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'username' => ['required', 'string', 'min:5', Rule::unique('dinetkan_admin')->where('shortname', multi_auth()->shortname)],
//        ]);

//        if ($validator->fails()) {
//            return response()->json([
//                'error' => $validator->errors(),
//            ]);
//        }
        $mitra = DinetkanAdmin::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->nama,
            'username' => $this->generateUniqueServiceId(), // $request->username,
            'password' => Hash::make($request->pass)
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $mitra,
        ]);
    }

    public function show(Mitra $mitra)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $mitra,
        ]);
    }

    public function update(Request $request, Mitra $mitra)
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
            $mitra->update([
                'name' => $request->nama_mitra,
                'id_mitra' => $request->id_mitra,
                'login' => $request->login,
                'user' => $request->user,
                'billing' => $request->billing,
                'nomor_wa' => $request->nomor_wa,
                'profile' => json_encode($request->profile),
            ]);
        } elseif ($request->profile == NULL) {
            $mitra->update([
                'name' => $request->nama_mitra,
                'id_mitra' => $request->id_mitra,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'user' => $request->user,
                'billing' => $request->billing,
                'nomor_wa' => $request->nomor_wa,
            ]);
        } elseif ($request->password == NULL && $request->profile == NULL) {
            $mitra->update([
                'name' => $request->nama_mitra,
                'id_mitra' => $request->id_mitra,
                'login' => $request->login,
                'user' => $request->user,
                'billing' => $request->billing,
                'nomor_wa' => $request->nomor_wa,
            ]);
        } else {
            $mitra->update([
                'name' => $request->nama_mitra,
                'id_mitra' => $request->id_mitra,
                'password' => Hash::make($request->password),
                'login' => $request->login,
                'user' => $request->user,
                'billing' => $request->billing,
                'nomor_wa' => $request->nomor_wa,
                'profile' => json_encode($request->profile),
            ]);
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $mitra,
        ]);
    }

    public function destroy($id)
    {
        $mitra = DinetkanAdmin::findOrFail($id);
        $mitra->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $mitra = DinetkanAdmin::where('id', $request->id);
        $mitra->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Mitra Berhasil Dinonaktifkan',
            'data' => $mitra,
        ]);
    }

    public function enable(Request $request)
    {
        $mitra = DinetkanAdmin::where('id', $request->id);
        $mitra->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Mitra Berhasil Diaktifkan',
            'data' => $mitra,
        ]);
    }

    function generateUniqueServiceId()
    {
        do {
            // 1. Generate nomor random (bisa angka saja atau kombinasi)
            $prefix = "AD";
            $randomNumber = mt_rand(1111, 9999); // Contoh: 6 digit angka
            $username = $prefix.$randomNumber;
            // 2. Cek apakah service_id tersebut sudah ada
            $exists = DinetkanAdmin::where('username', $username)->exists();

        } while ($exists); // Ulangi jika sudah ada

        return $username; // Kembalikan jika unik
    }
}
