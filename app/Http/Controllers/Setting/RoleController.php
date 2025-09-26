<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\Role;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $role = Role::where('shortname',multi_auth()->shortname)->first();
            return response()->json([
                'success' => true,
                'data' => $role,
            ]);
        }
        return view('backend.setting.role.index_new');
    }

    public function store(Request $request)
    {
        $role = Role::where('shortname',multi_auth()->shortname)->exists();
        if(!$role){
            $role = Role::create([
                'shortname' => multi_auth()->shortname,
            ]);
        }else{
            $role = Role::where('shortname',multi_auth()->shortname);
            $role->update([
                'teknisi_status_regist' => $request->teknisi_status_regist,
                'kasir_melihat_total_keuangan' => $request->kasir_melihat_total_keuangan,
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $role,
        ]);
    }
}
