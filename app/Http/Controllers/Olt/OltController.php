<?php

namespace App\Http\Controllers\Olt;

use App\Http\Controllers\Controller;
use App\Models\Olt\OltDevice;
use Illuminate\Http\Request;
use App\Library\HsgqAPI;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class OltController extends Controller
{
    private $hsgqApi;

    public function __construct()
    {
        $this->hsgqApi = new HsgqAPI();
    }

    public function index(Request $request)
    {
        session()->forget('olt_session');
        $auth = multi_auth();
        $shortname = $auth->shortname;
        // $olt = OltDevice::where('shortname', $shortname)->orderByDesc('id')->get();

        if ($request->ajax()) {
            $oltQuery = OltDevice::query()->where('shortname', $shortname)->orderByDesc('id');

            return DataTables::of($oltQuery)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($auth) {
                    return $this->getActionButtons($row, $auth->role);
                })
                ->toJson();
        }

        // Jika license_id == 2, tampilkan view limit account
        return $auth->license_id == 2 ? view('backend.account.limit') : view('backend.olt.index_new');
    }

    public function show($id)
    {
        $olt = OltDevice::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $olt,
        ]);
    }

    public function store(Request $request)
    {
        $auth = multi_auth();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('olt_device')->where('shortname', $auth->shortname)],
            'username' => 'required',
            'password' => 'required',
            'host' => ['required', Rule::unique('olt_device')],
        ]);

        $olt = OltDevice::create([
            'shortname' => $auth->shortname,
            'name' => $validated['name'],
            'type' => $request->type, // Pastikan input type sudah valid
            'host' => $validated['host'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'cookies' => '812u37y123y721y3', // Nilai default; pertimbangkan untuk membuat mekanisme generate cookie
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $olt,
        ]);
    }

    public function update(Request $request, OltDevice $olt)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'host' => 'required', // Menggunakan host, bukan ip
            'username' => 'required',
            'password' => 'required',
        ]);

        $olt->update([
            'name' => $validated['name'],
            'host' => $validated['host'],
            'type' => $request->type,
            'username' => $validated['username'],
            'password' => $validated['password'],
        ]);

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

    /**
     * Menghasilkan HTML tombol action untuk DataTables.
     *
     * @param  \App\Models\Olt\OltDevice  $row
     * @param  string  $role
     * @return string
     */
    private function getActionButtons($row, $role)
    {
        // Tombol login berdasarkan tipe perangkat
        $loginButton =
            $row->type === 'HIOSO 2 PON' || $row->type === 'HIOSO 4 PON'
            ? '<a href="javascript:void(0)" id="login-hioso" data-id="' . $row->id . '" class="me-2 btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <i class="ti ti-login"></i>
               </a>'
            : '<a href="javascript:void(0)" id="login-hsgq" data-id="' . $row->id . '" class="me-2 btn btn-primary" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <i class="ti ti-login"></i>
               </a>';

        // Jika role Admin, tambahkan tombol edit dan delete
        $adminButtons =
            $role === 'Admin'
            ? '<a href="javascript:void(0)" id="edit" data-id="' . $row->id . '" class="btn btn-warning text-white me-2" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <i class="ti ti-edit"></i>
               </a>
               <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" class="btn btn-danger" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                    <i class="ti ti-trash"></i>
               </a>'
            : '';

        return $loginButton . $adminButtons;
    }
}
