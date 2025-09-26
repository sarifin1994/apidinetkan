<?php

namespace App\Http\Controllers\Pppoe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pppoe\PppoeProfile;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Radius\RadiusProfile;

class PppoeProfileController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $profiles = PppoeProfile::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($profiles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->status === 1) {
                        return '
        <a href="javascript:void(0)" id="edit"
        data-id="' . $row->id . '"
        class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-pencil"></i>
        </a>
        <a href="javascript:void(0)" id="disable" data-id="' . $row->id . '"
        class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-user-off"></i>
        </a>
        <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '"
        class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-trash"></i>
        </a>';
                    } else {
                        return '
        <a href="javascript:void(0)" id="edit"
        data-id="' . $row->id . '"
        class="btn btn-secondary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-pencil"></i>
        </a>
        <a href="javascript:void(0)" id="enable" data-id="' . $row->id . '"
        class="btn btn-primary text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-user-check"></i>
        </a>
        <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '"
        class="btn btn-danger text-white" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-trash"></i>
        </a>';
                    }
                })
                ->toJson();
        }
        return view('backend.pppoe.profile.index_new');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:255', Rule::unique('pppoe_profile')->where('shortname', multi_auth()->shortname)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        if ($request->price !== null) {
            $price = str_replace('.', '', $request->price);
        } else {
            $price = 0;
        }
        if ($request->fee_mitra !== null) {
            $fee_mitra = str_replace('.', '', $request->fee_mitra);
        } else {
            $fee_mitra = 0;
        }
        $rateLimit = $request->rate;

        if ($request->rate === NULL && $request->groupProfile === NULL) {
            $profile = PppoeProfile::create([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
                // 'status' => $request->status,
            ]);
            $rprofile = 'NULL';
        } elseif ($request->rate !== NULL && $request->groupProfile === NULL) {
            $profile = PppoeProfile::create([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
                // 'status' => $request->status,
            ]);
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2, // 2 = PPPoE
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        } elseif ($request->rate === NULL && $request->groupProfile !== NULL) {
            $profile = PppoeProfile::create([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
                // 'status' => $request->status,
            ]);
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        } elseif ($request->rate !== NULL && $request->groupProfile !== NULL) {
            $profile = PppoeProfile::create([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
                // 'status' => $request->status,
            ]);
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2, // 2 = PPPoE
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $profile,
            $rprofile
        ]);
    }

    public function show(PppoeProfile $profile)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $profile,
        ]);
    }

    public function update(Request $request, PppoeProfile $profile)
    {
        if ($request->price !== null) {
            $price = str_replace('.', '', $request->price);
        } else {
            $price = 0;
        }
        if ($request->fee_mitra !== null) {
            $fee_mitra = str_replace('.', '', $request->fee_mitra);
        } else {
            $fee_mitra = 0;
        }
        $rateLimit = $request->rate;

        if ($request->rate === NULL && $request->groupProfile === NULL) {
            $profile->update([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile = 'NULL';
        } elseif ($request->rate !== NULL && $request->groupProfile === NULL) {
            $profile->update([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile_delete = RadiusProfile::where('shortname', multi_auth()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2, // 2 = PPPoE
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        } elseif ($request->rate === NULL && $request->groupProfile !== NULL) {
            $profile->update([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile_delete = RadiusProfile::where('shortname', multi_auth()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        } elseif ($request->rate !== NULL && $request->groupProfile !== NULL) {
            $profile->update([
                'shortname' => multi_auth()->shortname,
                'name' => $request->name,
                'price' => $price,
                'fee_mitra' => $fee_mitra,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile_delete = RadiusProfile::where('shortname', multi_auth()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2, // 2 = PPPoE
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $profile,
            $rprofile
        ]);
    }

    public function destroy($id)
    {
        $profile = PppoeProfile::findOrFail($id);
        $profile_nameArray = PppoeProfile::where('id', $id)->select('name')->first();
        $profile_name = $profile_nameArray->name;
        $rprofile = RadiusProfile::where('shortname', multi_auth()->shortname)
            ->where('profile', $profile_name)
            ->delete();
        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $profile = PppoeProfile::where('id', $request->id);
        $profile->update([
            'status' => 0,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Profile Berhasil Dinonaktifkan',
            'data' => $profile,
        ]);
    }

    public function enable(Request $request)
    {
        $profile = PppoeProfile::where('id', $request->id);
        $profile->update([
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Profile Berhasil Diaktifkan',
            'data' => $profile,
        ]);
    }
}
