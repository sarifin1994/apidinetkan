<?php

namespace App\Http\Controllers\Olt;

use App\Http\Controllers\Controller;
use App\Models\Olt\OltDevice;
use Illuminate\Http\Request;
use App\Library\HsgqAPI;
use App\Library\HiosoAPI;
use App\Models\OltUser;
use App\Models\Member;
use App\Models\RadiusSession;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class OltController extends Controller
{
    private $HsgqAPI;

    public function __construct()
    {
        $this->HsgqAPI = new HsgqAPI();
    }

    public function index()
    {
        if (request()->ajax()) {
            $olt = OltDevice::query()->where('shortname', multi_auth()->shortname)->orderBy('id', 'desc');
            return DataTables::of($olt)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->type === 'HIOSO') {
                        return '<a href="javascript:void(0)" id="login-hioso" data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">login</span>
                        </a>' .
                            (multi_auth()->role === 'Admin'
                                ? '
                        <a href="javascript:void(0)" id="edit" data-id="' .
                                    $row->id .
                                    '" class="btn btn-warning text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <a href="javascript:void(0)" id="delete" data-id="' .
                                    $row->id .
                                    '" class="btn btn-danger" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">delete</span>
                        </a>'
                                : '');
                    } else {
                        return '
                        <a href="javascript:void(0)" id="login-hsgq" data-id="' .
                            $row->id .
                            '" class="btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">login</span>
                        </a>' .
                            (multi_auth()->role === 'Admin'
                                ? '
                        <a href="javascript:void(0)" id="edit" data-id="' .
                                    $row->id .
                                    '" class="btn btn-warning text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <a href="javascript:void(0)" id="delete" data-id="' .
                                    $row->id .
                                    '" class="btn btn-danger" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                            <span class="material-symbols-outlined">delete</span>
                        </a>'
                                : '');
                    }
                })
                ->toJson();
        }
        if (multi_auth()->license_id == 2) {
            return view('backend.account.limit');
        } else {
            return view('backend.olt.index');
        }
    }  

    public function show($id)
    {
        //return response
        $olt = OltDevice::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $olt,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('olt_device')->where('shortname', multi_auth()->shortname)],
            'username' => 'required',
            'password' => 'required',
            'host' => ['required', Rule::unique('olt_device')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $olt = OltDevice::create([
            'shortname' => multi_auth()->shortname,
            'name' => $request->name,
            'type' => $request->type,
            'host' => $request->host,
            'username' => $request->username,
            'password' => $request->password,
            'cookies' => '812u37y123y721y3',
        ]);
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $olt,
        ]);
    }

    public function update(Request $request, OltDevice $olt)
    {
        $olt->update([
            'name' => $request->name,
            'host' => $request->ip,
            'username' => $request->username,
            'password' => $request->password,
        ]);

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $olt,
        ]);
    }

    public function destroy($id)
    {
        $olt = OltDevice::findOrFail($id);
        $olt->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
