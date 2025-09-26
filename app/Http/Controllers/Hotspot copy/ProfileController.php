<?php

namespace App\Http\Controllers\Hotspot;

use Illuminate\Http\Request;
use App\Models\RadiusProfile;
use App\Models\HotspotProfile;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\DataTables\Admin\Hotspot\ProfileDataTable;

class ProfileController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $profiles = HotspotProfile::query()->where('group_id', Auth::user()->id_group);
            return DataTables::of($profiles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="edit"
                    data-id="' .
                        $row->id .
                        '" class="badge b-ln-height badge-primary" title="Edit">
                        <i class="fa fa-edit"></i>

                </a>
                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' .
                        $row->id .
                        '" title="Delete">
                <i class="fa fa-trash"></i>
                </a>';
                })
                ->toJson();
        }

        $count = HotspotProfile::where('group_id', Auth::user()->id_group)->count();

        return view('hotspot.profile.index', compact('count'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('db_profile.profile_hs')->where('group_id', Auth::user()->id_group),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        if ($request->price === null) {
            $price = 0;
        } else {
            $price = str_replace('.', '', $request->price);
        }
        if ($request->reseller_price === null) {
            $reseller_price = 0;
        } else {
            $reseller_price = str_replace('.', '', $request->reseller_price);
        }
        if ($request->rate === null) {
            $rateLimit = 'Unlimited';
        } else {
            $rateLimit = $request->rate;
        }
        if ($request->uptime === null) {
            $uptime = 'Unlimited';
        } else {
            if ($request->satuan_uptime === 'Jam') {
                $uptime = $request->uptime * 3600;
            } elseif ($request->satuan_uptime === 'Hari') {
                $uptime = $request->uptime * 86400;
            } elseif ($request->satuan_uptime === 'Bulan') {
                $uptime_days = $request->uptime * 30;
                $uptime = $uptime_days * 86400;
            }
        }

        if ($request->validity === null) {
            $validity = 'Unlimited';
        } else {
            if ($request->satuan_validity === 'Jam') {
                $validity = $request->validity * 3600;
            } elseif ($request->satuan_validity === 'Hari') {
                $validity = $request->validity * 86400;
            } elseif ($request->satuan_validity === 'Bulan') {
                $validity_days = $request->validity * 30;
                $validity = $validity_days * 86400;
            }
        }

        if ($request->quota === null) {
            $quota = 'Unlimited';
        } else {
            if ($request->satuan_quota === 'MB') {
                $quota = $request->quota * 1048576;
            } elseif ($request->satuan_quota === 'GB') {
                // 1 GB = 1024 MB
                $quota_m = $request->quota * 1024;
                $quota = $quota_m * 1048576;
            }
        }

        $profile = HotspotProfile::create([
            'group_id' => Auth::user()->id_group,
            'shortname' => Auth::user()->shortname,
            'name' => $request->name,
            'price' => $price,
            'reseller_price' => $reseller_price,
            'rateLimit' => $rateLimit,
            'quota' => $quota,
            'uptime' => $uptime,
            'validity' => $validity,
            'shared' => $request->shared,
            'mac' => $request->lock_mac,
            'groupProfile' => $request->groupProfile,
        ]);
        $rprofile = RadiusProfile::create([
            'group_id' => Auth::user()->id_group,
            'shortname' => Auth::user()->shortname,
            'mode' => 1,
            'profile' => $request->name,
            'attribute' => 'Port-Limit',
            'value' => $request->shared,
        ]);
        if ($request->groupProfile !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }
        if ($request->rate !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        }
        if ($request->uptime !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Session-Timeout',
                'value' => $uptime,
            ]);
        }
        if ($request->validity !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Expiration',
                'value' => $validity,
            ]);
        }
        if ($request->quota !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Total-Limit',
                'value' => $quota,
            ]);
        }
        if ($request->lock_mac === '1') {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Calling-Station-Id',
                'value' => 'TRUE',
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $profile,
            $rprofile,
        ]);
    }

    public function show(HotspotProfile $profile)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $profile,
        ]);
    }

    public function update(Request $request, HotspotProfile $profile)
    {
        if ($request->price === null) {
            $price = 0;
        } else {
            $price = str_replace('.', '', $request->price);
        }
        if ($request->reseller_price === null) {
            $reseller_price = 0;
        } else {
            $reseller_price = str_replace('.', '', $request->reseller_price);
        }
        if ($request->rate === null) {
            $rateLimit = 'Unlimited';
        } else {
            $rateLimit = $request->rate;
        }
        if ($request->uptime === null) {
            $uptime = 'Unlimited';
        } else {
            if ($request->satuan_uptime === 'Jam') {
                $uptime = $request->uptime * 3600;
            } elseif ($request->satuan_uptime === 'Hari') {
                $uptime = $request->uptime * 86400;
            } elseif ($request->satuan_uptime === 'Bulan') {
                $uptime_days = $request->uptime * 30;
                $uptime = $uptime_days * 86400;
            }
        }

        if ($request->validity === null) {
            $validity = 'Unlimited';
        } else {
            if ($request->satuan_validity === 'Jam') {
                $validity = $request->validity * 3600;
            } elseif ($request->satuan_validity === 'Hari') {
                $validity = $request->validity * 86400;
            } elseif ($request->satuan_validity === 'Bulan') {
                $validity_days = $request->validity * 30;
                $validity = $validity_days * 86400;
            }
        }

        if ($request->quota === null) {
            $quota = 'Unlimited';
        } else {
            if ($request->satuan_quota === 'MB') {
                $quota = $request->quota * 1048576;
            } elseif ($request->satuan_quota === 'GB') {
                // 1 GB = 1024 MB
                $quota_m = $request->quota * 1024;
                $quota = $quota_m * 1048576;
            }
        }

        $profile->update([
            'name' => $request->name,
            'price' => $price,
            'reseller_price' => $reseller_price,
            'rateLimit' => $rateLimit,
            'quota' => $quota,
            'uptime' => $uptime,
            'validity' => $validity,
            'shared' => $request->shared,
            'mac' => $request->lock_mac,
            'groupProfile' => $request->groupProfile,
        ]);
        $rdelete = RadiusProfile::where('profile', $profile->name)->delete();
        $rprofile = RadiusProfile::create([
            'group_id' => Auth::user()->id_group,
            'shortname' => Auth::user()->shortname,
            'mode' => 1,
            'profile' => $request->name,
            'attribute' => 'Port-Limit',
            'value' => $request->shared,
        ]);
        if ($request->groupProfile !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }
        if ($request->rate !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        }
        if ($request->uptime !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Session-Timeout',
                'value' => $uptime,
            ]);
        }
        if ($request->validity !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Expiration',
                'value' => $validity,
            ]);
        }
        if ($request->quota !== null) {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Total-Limit',
                'value' => $quota,
            ]);
        }
        if ($request->lock_mac === '1') {
            $rprofile = RadiusProfile::create([
                'group_id' => Auth::user()->id_group,
                'shortname' => Auth::user()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Calling-Station-Id',
                'value' => 'TRUE',
            ]);
        }
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $profile,
            $rprofile,
            $rdelete,
        ]);
    }

    public function destroy($id)
    {
        $profile = HotspotProfile::findOrFail($id);
        $profile_nameArray = HotspotProfile::where('id', $id)->select('name')->first();
        $profile_name = $profile_nameArray->name;
        $rprofile = RadiusProfile::where('group_id', Auth::user()->id_group)->where('profile', $profile_name)->delete();
        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
