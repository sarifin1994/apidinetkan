<?php

namespace App\Http\Controllers\Admin\Service;

use App\Models\PppoeProfile;
use Illuminate\Http\Request;
use App\Models\RadiusProfile;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $profiles = PppoeProfile::query()->where('group_id', $request->user()->id_group);
            return DataTables::of($profiles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                        $row->id .
                        '" class="btn btn-sm btn-primary rounded-pill">
                        <i class="fas fa-edit"></i>

                </a>
                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' . $row->id . '">
                <i class="fas fa-trash-alt"></i>
                </a>';
                })
                ->toJson();
        }

        $count = PppoeProfile::where('group_id', $request->user()->id_group)->count();

        return view('pppoe.profile.index', [
            'count' => $count,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('db_profile.profile_pppoe')->where('group_id', $request->user()->id_group),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $price = str_replace('.', '', $request->price);
        $rateLimit = $request->rate;

        // null only
        if ($request->price === null && $request->groupProfile === null && $request->rate === null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'validity' => 'Unlimited',
                'rateLimit' => 'Unlimited',
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
            ]);

            // price only
        } elseif ($request->price !== null && $request->groupProfile === null && $request->rate === null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
            ]);

            // ratelimit only
        } elseif ($request->price === null && $request->groupProfile === null && $request->rate !== null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // group only
        } elseif ($request->groupProfile !== null && $request->price === null && $request->rate === null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // price and ratelimit
        } elseif ($request->price !== null && $request->rate !== null && $request->groupProfile === null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // price and group
        } elseif ($request->price !== null && $request->groupProfile !== null && $request->rate === null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // rate and group
        } elseif ($request->price === null && $request->groupProfile !== null && $request->rate !== null) {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);
        } else {
            $profile = PppoeProfile::create([
                'group_id' => $request->user()->id_group,
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);
        }
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
        $price = str_replace('.', '', $request->price);
        $rateLimit = $request->rate;
        // null only
        if ($request->price === null && $request->groupProfile === null && $request->rate === null) {
            $profile->update([
                'name' => $request->name,
                'validity' => 'Unlimited',
                'rateLimit' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
            ]);

            // price only
        } elseif ($request->price !== null && $request->groupProfile === null && $request->rate === null) {
            $profile->update([
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
            ]);

            // ratelimit only
        } elseif ($request->price === null && $request->groupProfile === null && $request->rate !== null) {
            $profile->update([
                'name' => $request->name,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // group only
        } elseif ($request->groupProfile !== null && $request->price === null && $request->rate === null) {
            $profile->update([
                'name' => $request->name,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // price and ratelimit
        } elseif ($request->price !== null && $request->rate !== null && $request->groupProfile === null) {
            $profile->update([
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();
            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // price and group
        } elseif ($request->price !== null && $request->groupProfile !== null && $request->rate === null) {
            $profile->update([
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => 'Unlimited',
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();

            $rprofile = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);

            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile,
            ]);

            // rate and group
        } elseif ($request->price === null && $request->groupProfile !== null && $request->rate !== null) {
            $profile->update([
                'name' => $request->name,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();

            $rprofile0 = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile1 = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile0,
                $rprofile1,
            ]);
        } else {
            $profile->update([
                'name' => $request->name,
                'price' => $price,
                'rateLimit' => $rateLimit,
                'validity' => 'Unlimited',
                'groupProfile' => $request->groupProfile,
            ]);
            $rdelete = RadiusProfile::where('shortname', $request->user()->shortname)->where('profile', $profile->name)->delete();

            $rprofile0 = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
            $rprofile1 = RadiusProfile::create([
                'group_id' => $request->user()->id_group,
                'shortname' => $request->user()->shortname,
                'mode' => 2,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
            //return response
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
                'data' => $profile,
                $rprofile0,
                $rprofile1,
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $profile = PppoeProfile::findOrFail($id);
        $profile_nameArray = PppoeProfile::where('id', $id)->select('name')->first();
        $profile_name = $profile_nameArray->name;
        $rprofile = RadiusProfile::where('group_id', $request->user()->id_group)->where('profile', $profile_name)->delete();
        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
