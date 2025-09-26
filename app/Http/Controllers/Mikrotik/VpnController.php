<?php

namespace App\Http\Controllers\Mikrotik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mikrotik\Vpn;
use App\Models\Mikrotik\VpnServer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use RouterOS\Client;
use RouterOS\Query;

class VpnController extends Controller
{
    public function index()
    {
        $vpnserver = VpnServer::where('status', 1)->get();
        if (request()->ajax()) {
            $vpns = Vpn::query()->where('shortname', multi_auth()->shortname);
            return DataTables::of($vpns)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
        <div class="btn-group" role="group">
            <a href="javascript:void(0)" id="show" data-id="' . $row->id . '" 
                class="btn btn-sm btn-primary" title="Lihat Script">
                <i class="ti ti-code-dots"></i> Script
            </a>
            <a href="javascript:void(0)" id="delete" data-id="' . $row->id . '" 
                class="btn btn-sm btn-danger text-white" title="Hapus">
                <i class="ti ti-trash"></i>
            </a>
        </div>
    ';
                })

                ->toJson();
        }
        return view('backend.radius.vpn.index_new', compact('vpnserver'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vpn_server' => 'required',
            'name' => 'required|string|min:3',
            'user' => 'required|string|min:3|unique:radius_vpn,user',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $server = VpnServer::where('host', $request->vpn_server)->select('host', 'user', 'password', 'port')->first();
        if (!$server) {
            return response()->json(
                [
                    'error' => 'VPN Server tidak ditemukan.',
                ],
                404,
            );
        }

        try {
            // Connect ke MikroTik
            $client = new Client([
                'host' => $server->host,
                'user' => $server->user,
                'pass' => $server->password,
                'port' => $server->port,
                'timeout' => 5,
            ]);

            // 1. Set local-address fix
            $localIp = '172.31.0.1';

            // 2. Cek apakah user sudah ada di MikroTik
            $queryCheckUser = (new Query('/ppp/secret/print'))->where('name', $request->user);
            $existingUser = $client->query($queryCheckUser)->read();
            if (!empty($existingUser)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'User sudah ada di MikroTik. Tidak bisa membuat duplikat.',
                    ],
                    409,
                );
            }

            // 3. Cari remote-address yang belum dipakai (maksimal 5x percobaan)
            $attempt = 0;
            do {
                $remoteIp = $this->generateRandomIp();

                $queryCheckRemoteIp = (new Query('/ppp/secret/print'))->where('remote-address', $remoteIp);
                $existingRemoteIp = $client->query($queryCheckRemoteIp)->read();

                $attempt++;
                if ($attempt > 5) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Gagal menemukan IP remote-address yang tersedia setelah beberapa percobaan.',
                        ],
                        500,
                    );
                }
            } while (!empty($existingRemoteIp));

            // 4. Kalau sudah dapat IP, buat PPP Secret
            $query = (new Query('/ppp/secret/add'))->equal('name', $request->user)->equal('password', $request->password)->equal('local-address', $localIp)->equal('remote-address', $remoteIp);

            $response = $client->query($query)->read();

            if (isset($response['!trap']) || isset($response['!fatal'])) {
                \Log::error('Gagal membuat PPP secret di MikroTik', ['response' => $response]);
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Gagal membuat VPN di MikroTik. Database tidak diubah.',
                    ],
                    500,
                );
            }

            // 5. Kalau sukses di MikroTik, baru buat database
            $vpn = Vpn::create([
                'shortname' => multi_auth()->shortname,
                'vpn_server' => $server->host,
                'name' => $request->name,
                'user' => $request->user,
                'password' => $request->password,
                'ip_address' => $remoteIp, // remote IP
                'local_address' => $localIp, // local IP fix 172.31.0.1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'VPN berhasil dibuat di MikroTik dan database.',
                'data' => $vpn,
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal konek ke MikroTik', ['error' => $e->getMessage()]);
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal konek ke MikroTik. Database tidak diubah.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function generateRandomIp()
    {
        $ip1 = 172;
        $ip2 = 31;
        $ip3 = rand(0, 255);
        $ip4 = rand(2, 254); // jangan 0,1 dan 255 biar valid

        return "$ip1.$ip2.$ip3.$ip4";
    }

    public function show($id)
    {
        // $server = VpnServer::select('host','user','password','port')->first();
        $vpn = Vpn::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $vpn,
            // 'server' => $server
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $vpn = Vpn::findOrFail($id);

        $server = VpnServer::where('host', $vpn->vpn_server)->select('host', 'user', 'password', 'port')->first();
        if (!$server) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'VPN Server tidak ditemukan.',
                ],
                404,
            );
        }

        try {
            // Connect ke MikroTik
            $client = new Client([
                'host' => $server->host,
                'user' => $server->user,
                'pass' => $server->password,
                'port' => $server->port,
            ]);

            // Cari PPP Secret berdasarkan nama user
            $query = (new Query('/ppp/secret/print'))->where('name', $vpn->user);
            $user = $client->query($query)->read();

            if (!empty($user) && !empty($user[0]['.id'])) {
                $userId = $user[0]['.id'];

                $query = (new Query('/ppp/secret/remove'))->equal('.id', $userId);
                $client->query($query)->read();
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'VPN User tidak ditemukan di MikroTik. Hapus dibatalkan.',
                    ],
                    404,
                );
            }

            // Kalau sudah berhasil hapus di MikroTik, baru hapus di database
            $vpn->delete();

            return response()->json([
                'success' => true,
                'message' => 'VPN berhasil dihapus dari MikroTik dan database.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus PPP secret di MikroTik', ['error' => $e->getMessage()]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal koneksi ke MikroTik. VPN tidak dihapus dari database.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
