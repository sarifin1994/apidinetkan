<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Vpn;
use RouterOS\Query;
use RouterOS\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class VpnMikrotikController extends Controller
{
    // Define the VPN IP pool range
    private const IP_POOL_START = '172.16.0.100';
    private const IP_POOL_END   = '172.16.7.250';

    /**
     * Display a listing of the VPNs.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vpns = Vpn::query()->where('group_id', $request->user()->id_group);
            return DataTables::of($vpns)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('settings.mikrotik.vpn.partials.actions', compact('row'))->render();
                })
                ->toJson();
        }

        return view('settings.mikrotik.vpn.index');
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
            'user'     => 'required|string|min:3|unique:db_profile.vpn,user',
            'password' => 'required|string|min:3',
        ], [
            'name.required' => 'VPN name is required.',
            'user.required' => 'User is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = $this->getRouterOsClient();

            // Fetch all existing PPP secrets to find which IPs are in use
            $query = new Query('/ppp/secret/print');
            $response = $client->query($query)->read();

            // Extract used addresses
            $usedAddresses = [];
            foreach ($response as $entry) {
                if (isset($entry['remote-address'])) {
                    $usedAddresses[] = $entry['remote-address'];
                }
            }

            // Generate the IP pool
            $ipPool = $this->generateIpPool(self::IP_POOL_START, self::IP_POOL_END);

            // Find the first available IP from the pool
            $availableIp = $this->findFirstAvailableIp($ipPool, $usedAddresses);

            if (!$availableIp) {
                // No available IP
                return response()->json([
                    'success' => false,
                    'message' => 'No available IPs in the defined pool range.',
                ], 500);
            }

            // Add the new secret to the RouterOS
            $query = (new Query('/ppp/secret/add'))
                ->equal('name', $request->user)
                ->equal('password', $request->password)
                ->equal('remote-address', $availableIp);

            $client->query($query)->read();

            // Create VPN record in the database
            $vpn = Vpn::create([
                'group_id'  => $request->user()->id_group,
                'name'      => $request->name,
                'user'      => $request->user,
                'password'  => $request->password,
                'ip_address' => $availableIp,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'VPN successfully created.',
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
        $vpn = Vpn::findOrFail($id);

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
            $vpn = Vpn::findOrFail($id);

            // Initialize RouterOS client
            $client = $this->getRouterOsClient();

            // Remove VPN from RouterOS
            $this->removeVpnFromRouterOs($client, $vpn->user);

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
            'host' => '202.47.185.237',
            'user' => 'vpn',
            'pass' => 'Lancar2020',
            'port' => 8786,
        ]);
    }

    /**
     * Remove VPN from RouterOS.
     *
     * @param  \RouterOS\Client  $client
     * @param  string  $username
     * @return void
     */
    private function removeVpnFromRouterOs(Client $client, string $username): void
    {
        $query = (new Query('/ppp/secret/print'))->where('name', $username);
        $users = $client->query($query)->read();

        if (!empty($users[0]['.id'])) {
            $removeQuery = (new Query('/ppp/secret/remove'))->equal('.id', $users[0]['.id']);
            $client->query($removeQuery)->read();
        }
    }

    /**
     * Generate a list of IP addresses from start to end.
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    private function generateIpPool(string $start, string $end): array
    {
        $startLong = ip2long($start);
        $endLong   = ip2long($end);

        $ips = [];
        for ($ip = $startLong; $ip <= $endLong; $ip++) {
            $ips[] = long2ip($ip);
        }

        return $ips;
    }

    /**
     * Find the first unused IP from the pool.
     *
     * @param array $pool
     * @param array $used
     * @return string|null
     */
    private function findFirstAvailableIp(array $pool, array $used): ?string
    {
        // Create a lookup set for O(1) containment checks
        $usedSet = array_flip($used);

        foreach ($pool as $ip) {
            if (!isset($usedSet[$ip])) {
                return $ip;
            }
        }

        return null;
    }
}
