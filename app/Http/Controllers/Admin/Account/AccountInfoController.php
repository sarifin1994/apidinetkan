<?php

namespace App\Http\Controllers\Admin\Account;

use App\Models\Vpn;
use App\Models\User;
use App\Models\OltDevice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\VpnRemote;
use App\Models\HotspotUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountInfoController extends Controller
{
    public function index(Request $request)
    {
        $license = ($request->user()->load('license'))->license;
        $staffUsers = User::where('id_group', $request->user()->id_group)->where('role', '!=', 'Admin')->count();
        $nas = RadiusNas::where('group_id', $request->user()->id_group)->count();
        $user_hs = HotspotUser::where('group_id', $request->user()->id_group)->count();
        $user_pppoe = PppoeUser::where('group_id', $request->user()->id_group)->count();
        $vpn = Vpn::where('group_id', $request->user()->id_group)->count();
        $vpnRemote = VpnRemote::where('group_id', $request->user()->id_group)->count();
        $eponOlt = OltDevice::where('group_id', $request->user()->id_group)->where('type', 'epon')->count();
        $gponOlt = OltDevice::where('group_id', $request->user()->id_group)->where('type', 'gpon')->count();

        $staffUsersPercentage = (isset($license->limit_user) && $license->limit_user != 0) ? ($staffUsers / $license->limit_user) * 100 : 0;
        $nasPercentage = (isset($license->limit_nas) && $license->limit_nas != 0) ? ($nas / $license->limit_nas) * 100 : 0;
        $userHsPercentage = (isset($license->limit_hs) && $license->limit_hs != 0) ? ($user_hs / $license->limit_hs) * 100 : 0;
        $userPppoePercentage = (isset($license->limit_pppoe) && $license->limit_pppoe != 0) ? ($user_pppoe / $license->limit_pppoe) * 100 : 0;
        $vpnPercentage = (isset($license->limit_vpn) && $license->limit_vpn != 0) ? ($vpn / $license->limit_vpn) * 100 : 0;
        $vpnRemotePercentage = (isset($license->limit_vpn_remote) && $license->limit_vpn_remote != 0) ? ($vpnRemote / $license->limit_vpn_remote) * 100 : 0;
        $eponOltPercentage = (isset($license->olt_epon_limit) && $license->olt_epon_limit != 0) ? ($eponOlt / $license->olt_epon_limit) * 100 : 0;
        $gponOltPercentage = (isset($license->olt_gpon_limit) && $license->olt_gpon_limit != 0) ? ($gponOlt / $license->olt_gpon_limit) * 100 : 0;

        return view('accounts.info.index', compact(
            'license',
            'staffUsers',
            'nas',
            'user_hs',
            'user_pppoe',
            'vpn',
            'vpnRemote',
            'eponOlt',
            'gponOlt',
            'staffUsersPercentage',
            'nasPercentage',
            'userHsPercentage',
            'userPppoePercentage',
            'vpnPercentage',
            'vpnRemotePercentage',
            'eponOltPercentage',
            'gponOltPercentage'
        ));
    }
}
