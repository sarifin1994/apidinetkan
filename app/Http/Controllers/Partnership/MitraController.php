<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\Invoice;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\LicenseDinetkan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Partnership\Mitra;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Pppoe\PppoeUser;

use App\Models\Pppoe\PppoeProfile;

class MitraController extends Controller
{
    public function index()
    {
        $profiles = PppoeProfile::where('shortname', multi_auth()->shortname)->get();
        $licensedinetkan = [];
        if (multi_auth()->is_dinetkan == 1 || multi_auth()->ext_role == "dinetkan") {
            $licensedinetkan = LicenseDinetkan::get()->map(function ($item) {
                $obj = new \stdClass();
                $obj->id_dinetkan = $item->id . '_dinetkan';
                $obj->name = $item->name;
                return $obj;
            });
            $licensedinetkan = json_decode(json_encode($licensedinetkan->values()));
        }
        if (request()->ajax()) {
            $mitras = Mitra::query()
                ->where('shortname', multi_auth()->shortname)
                ->orderBy('id', 'desc');
            return DataTables::of($mitras)
                ->addIndexColumn()
                ->addColumn('jml_plgn', function ($row) {
                    return $row
                        ->hasMany(PppoeUser::class, 'mitra_id', 'id')
                        ->where('shortname', multi_auth()->shortname)
                        ->count();
                })
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
                    $editSaldoBtn = '
        <a href="javascript:void(0)" id="edit_komisi" data-id="' . $row->id . '"
            class="btn btn-primary btn-sm text-white me-1">
            <i class="ti ti-currency-dollar"></i>
        </a>';

                    if ($row->status === 1) {
                        $disableBtn = '
            <a href="javascript:void(0)" id="disable" data-id="' . $row->id . '"
                class="btn btn-primary btn-sm text-white me-1">
                <i class="ti ti-user-off"></i>
            </a>';
                        return $editBtn . $disableBtn . $deleteBtn . $editSaldoBtn;
                    } else {
                        $enableBtn = '
            <a href="javascript:void(0)" id="enable" data-id="' . $row->id . '"
                class="btn btn-primary btn-sm text-white me-1">
                <i class="ti ti-user-check"></i>
            </a>';
                        return $editBtn . $enableBtn . $deleteBtn . $editSaldoBtn;
                    }
                })

                ->toJson();
        }
        return view('backend.partnership.mitra.index_new', compact('profiles', 'licensedinetkan'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_mitra' => ['required', 'string', 'min:5', 'max:10', Rule::unique('partnership_mitra')->where('shortname', multi_auth()->shortname)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $mitra = Mitra::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->nama_mitra,
            'id_mitra' => $request->id_mitra,
            'password' => Hash::make($request->password),
            'login' => $request->login,
            'user' => $request->user,
            'billing' => $request->billing,
            'nomor_wa' => $request->nomor_wa,
            'profile' => json_encode($request->profile),
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
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $mitra = Mitra::where('id', $request->id);
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
        $mitra = Mitra::where('id', $request->id);
        $mitra->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Mitra Berhasil Diaktifkan',
            'data' => $mitra,
        ]);
    }

    public function get_invoice_paid($id){
        if (request()->ajax()) {
            $mitra = Mitra::query()->where('id', $id)->first();
            $inv = Invoice::query()
                ->where('mitra_id', $mitra->id)
                ->where('status', 'paid')
                ->get();
            return response()->json($inv);
        }
    }

    public function edit_saldo(Request $request){
        DB::beginTransaction();
        try{
            $jenis = $request->jenis;
            $invoice = Invoice::query()->where('id', $request->id_invoice)->first();
            $mitra = Mitra::where('id', $request->mitra_id)->first();
            // $shortname = $invoice->shortname;
            $transaksi = TransaksiMitra::create([
                'shortname' => $mitra->shortname,
                'mitra_id' => $mitra->id,
                'id_data' => $invoice ? $invoice->id : 0,
                'tanggal' => Carbon::now(),
                'tipe' => $jenis == 'tambah' ? 'Pemasukan' : 'Pengeluaran',
                'kategori' => 'Komisi',
                'deskripsi' => $request->notes,
                'nominal' => $request->nominal_mitra_edit_komisi,
                'metode' => "manual by admin",
                'created_by' => multi_auth()->shortname,
            ]);

            $balancehistory = Balancehistory::create([
                'id_mitra' => $mitra->id,
                'id_reseller' => '',
                'tx_amount' => $request->nominal_mitra_edit_komisi,
                'notes' => $request->notes,
                'type' => 'in',
                'tx_date' => Carbon::now(),
                'id_transaksi' => $transaksi->id
            ]);

            $updatemitra = Mitra::where('id', $mitra->id)->first();
            if($jenis == 'tambah'){
                if($updatemitra){
                    $lastbalance = $updatemitra->balance;
                    $updatemitra->update([
                        'balance' => $lastbalance + (int)$request->nominal_mitra_edit_komisi
                    ]);
                }
            }
            if($jenis == 'kurang'){
                if($updatemitra){
                    $lastbalance = $updatemitra->balance;
                    $updatemitra->update([
                        'balance' => $lastbalance - (int)$request->nominal_mitra_edit_komisi
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Komisi berhasil di ubah',
                'success' => true
            ], 201);
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(['message' => 'Error creating: ' . $ex->getMessage()], 500);
        }
    }
}
