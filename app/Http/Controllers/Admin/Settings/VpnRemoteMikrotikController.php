<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Vpn;
use RouterOS\Query;
use RouterOS\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\VpnRemote;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VpnRemoteMikrotikController extends Controller
{
    /**
     * Display a listing of the VPNs.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vpns = VpnRemote::query()->where('group_id', $request->user()->id_group);
            return DataTables::of($vpns)
                ->addIndexColumn()
                ->editColumn('remote_address', function ($row) {
                    return 'go.radiusqu.com' . ':' . $row->dst_port;
                })
                ->addColumn('action', function ($row) {
                    return view('settings.mikrotik.vpn-remote.partials.actions', compact('row'))->render();
                })
                ->toJson();
        }

        $vpns = Vpn::query()->where('group_id', $request->user()->id_group)->get();

        return view('settings.mikrotik.vpn-remote.index', compact('vpns'));
    }

    /**
     * Store a newly created VPN in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|min:3|unique:db_profile.vpn,name',
            'dst_port'     => 'required|string|digits:5',
            'to_addresses'     => 'required|string|max:20',
            'to_ports'     => 'required|string|min_digits:2',
        ], [
            'name.required' => 'VPN name is required.',
            'dst_port.required' => 'Destination Port is required.',
            'to_addresses.required' => 'To Addresses is required.',
            'to_ports.required' => 'To Ports is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $license = ($request->user()->load('license'))->license;
        $existingVpn = VpnRemote::where('group_id', $request->user()->id_group)->count();

        if ($existingVpn >= $license->limit_vpn_remote && $license->limit_vpn_remote) {
            return response(400)->json([
                'message' => 'Limit VPN telah tercapai, silahkan upgrade lisensi anda',
            ]);
        }

        try {
            $comment = "Forward TCP {$request->dst_port} to {$request->to_addresses}:{$request->to_ports}";
            $protocol = 'tcp';

            $vpn = VpnRemote::create([
                'group_id'  => $request->user()->id_group,
                'name'      => $request->name,
                'dst_port'  => $request->dst_port,
                'to_addresses'  => $request->to_addresses,
                'to_ports'  => $request->to_ports,
                'comment'   => $comment,
                'protocol'  => $protocol,
            ]);

            $client = $this->getRouterOsClient();

            $query = (new Query('/ip/firewall/nat/add'))
                ->equal('chain', 'dstnat')
                ->equal('action', 'dst-nat')
                ->equal('protocol', $protocol)
                ->equal('dst-port', $request->dst_port)
                ->equal('to-addresses', $request->to_addresses)
                ->equal('to-ports', $request->to_ports)
                ->equal('comment', $comment);

            $client->query($query)->read();

            return response()->json([
                'success' => true,
                'message' => 'VPN Remote successfully created.',
                'data'    => $vpn,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error creating VPN: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the VPN.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified VPN.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $vpn = VpnRemote::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'VPN details retrieved successfully.',
            'data'    => $vpn,
        ]);
    }

    /**
     * Remove the specified VPN from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $vpn = VpnRemote::findOrFail($id);

            // Initialize RouterOS client
            $client = $this->getRouterOsClient();

            // Remove VPN from RouterOS
            $this->removeVpnFromRouterOs($client, $vpn->comment);

            // Delete VPN record from the database
            $vpn->delete();

            return response()->json([
                'success' => true,
                'message' => 'VPN successfully deleted.',
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting VPN: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the VPN.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initialize and return the RouterOS client.
     *
     * @return \RouterOS\Client
     */
    private function getRouterOsClient()
    {
        return new Client([
            'host' => 'vpn.radiusqu.com',
            'user' => 'vpn',
            'pass' => 'Lancar2020Mantap',
            'port' => 8786,
        ]);
    }

    /**
     * Remove VPN from RouterOS.
     *
     * @param  \RouterOS\Client  $client
     * @param  string  $comment
     * @return void
     */
    private function removeVpnFromRouterOs(Client $client, string $comment): void
    {
        // First find the NAT rule ID
        $findQuery = (new Query('/ip/firewall/nat/print'))
            ->where('comment', $comment);

        $response = $client->query($findQuery)->read();

        if (!empty($response)) {
            // Get the .id from the first matching rule
            $id = $response[0]['.id'];

            // Remove the rule using its ID
            $removeQuery = (new Query('/ip/firewall/nat/remove'))
                ->equal('numbers', $id);

            $client->query($removeQuery)->read();
        }
    }
}
