<?php

namespace App\Http\Controllers\Reseller;

use App\Models\HotspotUser;
use Illuminate\Http\Request;
use App\Models\HotspotReseller;
use App\Http\Controllers\Controller;
use App\Models\HotspotTransaksiReseller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reseller = HotspotReseller::where('id', $request->user()->reseller_id)->first();
        $user = HotspotUser::where('group_id', $request->user()->id_group)->where('reseller_id', $request->user()->reseller_id)->count();
        $komisi = HotspotTransaksiReseller::where('group_id', $request->user()->id_group)->where('type', 2)->where('reseller_id', $request->user()->reseller_id)->sum('komisi');

        return view('dashboards.reseller', compact(
            'reseller',
            'user',
            'komisi'
        ));
    }
}
