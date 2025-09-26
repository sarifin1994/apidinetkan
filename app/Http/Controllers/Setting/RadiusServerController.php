<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\RadiusServer;
use Yajra\DataTables\Facades\DataTables;

class RadiusServerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $vpns = VpnServer::query();
            return DataTables::of($vpns)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                        $row->id .
                        '" class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                        <span class="material-symbols-outlined">edit</span>
                </a>
                <a href="javascript:void(0)" id="delete" data-id="' .
                        $row->id .
                        '" class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                <span class="material-symbols-outlined">delete</span>
                </a>';
                })
                ->toJson();
        }
        return view('backend.setting.vpn.index');
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'kode_area' => [
        //         'required',
        //         'string',
        //         'min:2',
        //         'max:255',
        //         Rule::unique('mapping_area')->where('shortname', multi_auth()->shortname),
        //     ],
        //     'deskripsi' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        $vpns = VpnServer::create([
            'lokasi' => $request->lokasi,
            'name' => $request->name,
            'host' => $request->host,
            'user' => $request->user,
            'password' => $request->password,
            'port' => $request->port,
            'status' => 1,
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $vpns,
        ]);
    }

    public function show(VpnServer $vpn)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $vpn,
        ]);
    }

    public function update(Request $request, VpnServer $vpn)
    {
        // $validator = Validator::make($request->all(), [
        //     // 'kode_area' => ['required', 'string','min:2','max:255', 'unique:'.Pop::class],
        //     'kode_area' => 'required',
        //     'deskripsi' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors(),
        //     ]);
        // }

        $vpn->update([
            // 'lokasi' => $request->lokasi,
            // 'name' => $request->name,
            // 'host' => $request->host,
            'user' => $request->user,
            'password' => $request->password,
            'port' => $request->port,
            'status' => 1,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $vpn,
        ]);
    }

    public function destroy($id)
    {
        $vpn = VpnServer::findOrFail($id);
        $vpn->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
