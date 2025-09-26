<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\WaServer;
use Yajra\DataTables\Facades\DataTables;

class WaServerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $wa = WaServer::query();
            return DataTables::of($wa)
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
        return view('backend.setting.whatsapp.index');
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

        $wa = WaServer::create([
            'wa_url' => $request->wa_url,
            'wa_api' => $request->wa_api,
            'wa_sender' => $request->wa_sender,
            'status' => 1,
            'wa_server' => strtolower($request->wa_server)
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $wa,
        ]);
    }

    public function show(WaServer $wa)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $wa,
        ]);
    }

    public function update(Request $request, WaServer $wa)
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

        $wa->update([
            // 'lokasi' => $request->lokasi,
            // 'name' => $request->name,
            // 'host' => $request->host,
            'wa_api' => $request->wa_api,
            'wa_sender' => $request->wa_sender,
            'wa_server' => strtolower($request->wa_server)
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $wa,
        ]);
    }

    public function destroy($id)
    {
        $wa = WaServer::findOrFail($id);
        $wa->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
