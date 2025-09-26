<?php

namespace App\Http\Controllers\Hotspot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotspot\HotspotProfile;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Radius\RadiusProfile;

class HotspotProfileController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $profiles = HotspotProfile::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($profiles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->status === 1) {
                        return '
        <a href="javascript:void(0)" id="edit"
            data-id="' . $row->id . '"
            class="btn btn-secondary text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-edit"></i>
        </a>
        <a href="javascript:void(0)" id="disable"
            data-id="' . $row->id . '"
            class="btn btn-primary text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-user-off"></i>
        </a>
        <a href="javascript:void(0)" id="delete"
            data-id="' . $row->id . '"
            class="btn btn-danger text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-trash"></i>
        </a>';
                    } else {
                        return '
        <a href="javascript:void(0)" id="edit"
            data-id="' . $row->id . '"
            class="btn btn-secondary text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-edit"></i>
        </a>
        <a href="javascript:void(0)" id="enable"
            data-id="' . $row->id . '"
            class="btn btn-primary text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-user-check"></i>
        </a>
        <a href="javascript:void(0)" id="delete"
            data-id="' . $row->id . '"
            class="btn btn-danger text-white"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
            <i class="ti ti-trash"></i>
        </a>';
                    }
                })
                ->toJson();
        }
        return view('backend.hotspot.profile.index_new');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:255', Rule::unique('hotspot_profile')->where('shortname', multi_auth()->shortname)],
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
            'shortname' => multi_auth()->shortname,
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
            'is_billing' => $request->is_billing,
        ]);
        $rprofile = RadiusProfile::create([
            'shortname' => multi_auth()->shortname,
            'mode' => 1,
            'profile' => $request->name,
            'attribute' => 'Port-Limit',
            'value' => $request->shared,
        ]);
        if ($request->groupProfile !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }
        if ($request->rate !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        }
        if ($request->uptime !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Session-Timeout',
                'value' => $uptime,
            ]);
        }
        if ($request->validity !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Expiration',
                'value' => $validity,
            ]);
        }
        if ($request->quota !== null) {
            if ($request->quota == 4 && $request->satuan_quota === 'GB') {
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit-Gigawords',
                    'value'     => 1, // 1 Gigaword untuk 4GB
                ]);
            } elseif ($request->quota > 4 && $request->satuan_quota === 'GB') {
                // Hitung jumlah full gigawords
                $gigawords = floor($request->quota / 4);
                // Hitung sisa kuota dalam GB setelah dikurangi gigawords penuh
                $remainderGB = $request->quota - ($gigawords * 4);
                // Konversi sisa kuota ke byte (1GB = 1073741824 byte)
                $totalLimit = $remainderGB * 1073741824;

                // Buat atribut Gigawords dengan nilai full gigawords
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit-Gigawords',
                    'value'     => $gigawords,
                ]);

                // Jika ada sisa (lebih dari 0GB), buat atribut Total-Limit untuk sisa kuota
                if ($totalLimit > 0) {
                    $rprofile = RadiusProfile::create([
                        'shortname' => multi_auth()->shortname,
                        'mode'      => 1,
                        'profile'   => $request->name,
                        'attribute' => 'Mikrotik-Total-Limit',
                        'value'     => $totalLimit,
                    ]);
                }
            } else {
                if ($request->satuan_quota === 'MB') {
                    $quotaInBytes = $request->quota * 1048576;
                } elseif ($request->satuan_quota === 'GB') {
                    // 1 GB = 1024 MB
                    $quota_m = $request->quota * 1024;
                    $quotaInBytes = $quota_m * 1048576;
                }
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit',
                    'value'     => $quotaInBytes,
                ]);
            }
        }
        if ($request->lock_mac === '1') {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
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
        $rdelete = RadiusProfile::where('shortname', multi_auth()->shortname)->where('profile', $profile->name)->delete();
        $rprofile = RadiusProfile::create([
            'shortname' => multi_auth()->shortname,
            'mode' => 1,
            'profile' => $request->name,
            'attribute' => 'Port-Limit',
            'value' => $request->shared,
        ]);
        if ($request->groupProfile !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Group',
                'value' => $request->groupProfile,
            ]);
        }
        if ($request->rate !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Mikrotik-Rate-Limit',
                'value' => $rateLimit,
            ]);
        }
        if ($request->uptime !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Session-Timeout',
                'value' => $uptime,
            ]);
        }
        if ($request->validity !== null) {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
                'mode' => 1,
                'profile' => $request->name,
                'attribute' => 'Expiration',
                'value' => $validity,
            ]);
        }
        if ($request->quota !== null) {
            if ($request->quota == 4 && $request->satuan_quota === 'GB') {
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit-Gigawords',
                    'value'     => 1, // 1 Gigaword untuk 4GB
                ]);
            } elseif ($request->quota > 4 & $request->satuan_quota === 'GB') {
                // Hitung jumlah full gigawords
                $gigawords = floor($request->quota / 4);
                // Hitung sisa kuota dalam GB setelah dikurangi gigawords penuh
                $remainderGB = $request->quota - ($gigawords * 4);
                // Konversi sisa kuota ke byte (1GB = 1073741824 byte)
                $totalLimit = $remainderGB * 1073741824;

                // Buat atribut Gigawords dengan nilai full gigawords
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit-Gigawords',
                    'value'     => $gigawords,
                ]);

                // Jika ada sisa (lebih dari 0GB), buat atribut Total-Limit untuk sisa kuota
                if ($totalLimit > 0) {
                    $rprofile = RadiusProfile::create([
                        'shortname' => multi_auth()->shortname,
                        'mode'      => 1,
                        'profile'   => $request->name,
                        'attribute' => 'Mikrotik-Total-Limit',
                        'value'     => $totalLimit,
                    ]);
                }
            } else {
                // Jika kuota kurang dari 4GB, langsung konversi ke byte
                if ($request->satuan_quota === 'MB') {
                    $quotaInBytes = $request->quota * 1048576;
                } elseif ($request->satuan_quota === 'GB') {
                    // 1 GB = 1024 MB
                    $quota_m = $request->quota * 1024;
                    $quotaInBytes = $quota_m * 1048576;
                }
                $rprofile = RadiusProfile::create([
                    'shortname' => multi_auth()->shortname,
                    'mode'      => 1,
                    'profile'   => $request->name,
                    'attribute' => 'Mikrotik-Total-Limit',
                    'value'     => $quotaInBytes,
                ]);
            }
        }
        if ($request->lock_mac === '1') {
            $rprofile = RadiusProfile::create([
                'shortname' => multi_auth()->shortname,
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
        $rprofile = RadiusProfile::where('shortname', multi_auth()->shortname)->where('profile', $profile_name)->delete();
        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }

    public function disable(Request $request)
    {
        $profile = HotspotProfile::where('id', $request->id);
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
        $profile = HotspotProfile::where('id', $request->id);
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
