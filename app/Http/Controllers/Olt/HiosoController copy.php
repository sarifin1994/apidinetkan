<?php

namespace App\Http\Controllers\Olt;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Process;
use App\Library\HiosoAPI;
use App\Models\Olt\OltDevice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HiosoController extends Controller
{
    public function do_auth_device(Request $request)
    {
        try {
            $api = new HiosoAPI($request->id); // akan otomatis login & simpan session
        } catch (\Exception $e) {
            return back()->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    public function dashboard(Request $request)
    {
        $device = session('olt_session');
        if (!$device) {
            return redirect('/olt');
        }
        // Ambil data dari session (karena sebelumnya sudah login)
        $api = new HiosoAPI(); // otomatis ambil dari session
        $data = $api->getSystemInfo();

        if ($request->ajax()) {
            try {
                $allOnu = [];

                if ($device->type === 'HIOSO 2 PON') {
                    // $pon_real = '0/1/' . $i;
                    // $url = 'http://' . $device['host'] . '/onuConfigOnuList.asp?oltponno=' . $pon_real;
                } else {
                    $url = 'http://' . $device['host'] . '/onuAllPonOnuList.asp';
                }
                try {
                    $response = Http::withBasicAuth($device['username'], $device['password'])
                        ->withOptions(['verify' => false])
                        ->get($url);

                    if ($process->successful()) {
                        $html = $response->body();
                        $parsed = $this->parseOnutableScript($html);

                        $allOnu = array_merge($allOnu, $parsed);
                    }
                } catch (\Exception $e) {
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => $e->getMessage(),
                        ],
                        500,
                    );
                }

                return DataTables::of(collect($allOnu))->addIndexColumn()->toJson();
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ],
                    500,
                );
            }
        }

        return view('backend.olt.hioso.dashboard', compact('data'));
    }

    // public function fetchWebOverview(Request $request)
    // {
    //     if (request()->ajax()) {
    //         $device = OltDevice::where('id', $request->id)->first();
    //         $url = $device->host;
    //         try {
    //             $url = 'http://' . $url . '/onuOverviewPonList.asp';
    //             $username = $device->username; // ganti sesuai setting OLT
    //             $password = $device->password;

    //             // Basic Auth untuk login ke OLT
    //             $response = Http::withBasicAuth($username, $password)
    //                 ->withOptions(['verify' => false])
    //                 ->get($url);

    //             $html = $response->body();
    //             $ponData = $this->parsePonList($html);
    //             return DataTables::of($ponData)->addIndexColumn()->toJson();
    //         } catch (\Exception $e) {
    //             return response()->json(
    //                 [
    //                     'status' => 'error',
    //                     'message' => $e->getMessage(),
    //                 ],
    //                 500,
    //             );
    //         }
    //     }
    //     return view('backend.olt.hioso.dashboard');
    // }

    public function showOnu(Request $request, $pon_id)
    {
        // Ambil device dari session
        $device = session('olt_session');
        if (!$device) {
            abort(403, 'OLT belum login.');
        }

        if ($request->has('draw')) {
            if ($device->type === 'HIOSO 2 PON') {
                $pon_real = '0/1/' . $pon_id;
                $url = 'http://' . $device['host'] . '/onuConfigOnuList.asp?oltponno=' . $pon_real;
            } else {
                $pon_real = '0/' . $pon_id;
                $url = 'http://' . $device['host'] . '/onuOverview.asp?oltponno=' . urlencode($pon_real);
            }

            $response = Http::withBasicAuth($device['username'], $device['password'])
                ->withOptions(['verify' => false])
                ->get($url);

            $html = $response->body();
            $data = $this->parseOnutableScript($html);

            return DataTables::of(collect($data))->addIndexColumn()->toJson();
        }

        return view('backend.olt.hioso.onu', [
            'olt_id' => $device['id'],
            'ports' => $pon_id,
            'data' => [
                'pon' => [['port_id' => 1], ['port_id' => 2], ['port_id' => 3], ['port_id' => 4]],
            ],
        ]);
    }

    public function showAllOnu(Request $request)
    {
        $device = session('olt_session');

        if (!$device) {
            abort(403, 'OLT belum login.');
        }

        if ($request->has('draw')) {
            $allOnu = [];

            // Misal PON 1 sampai 4
            for ($i = 1; $i <= 4; $i++) {
                if ($device->type === 'HIOSO 2 PON') {
                    $pon_real = '0/1/' . $i;
                    $url = 'http://' . $device['host'] . '/onuConfigOnuList.asp?oltponno=' . $pon_real;
                } else {
                    $pon_real = '0/' . $i;
                    $url = 'http://' . $device['host'] . '/onuAllPonOnuList.asp';
                }
                try {
                    $response = Http::withBasicAuth($device['username'], $device['password'])
                        ->withOptions(['verify' => false])
                        ->get($url);
                    // dd($response->body());

                    if ($process->successful()) {
                        $html = $response->body();
                        $parsed = $this->parseOnutableScript($html);

                        // Tambahkan info PON ID ke setiap ONU
                        foreach ($parsed as &$onu) {
                            $onu['pon_id'] = $i;
                        }

                        $allOnu = array_merge($allOnu, $parsed);
                    }
                } catch (\Exception $e) {
                    // Abaikan error per pon jika gagal
                    continue;
                }
            }

            return DataTables::of(collect($allOnu))->addIndexColumn()->toJson();
        }

        return view('backend.olt.hioso.onu', [
            'olt_id' => $device['id'],
            'ports' => '',
            'data' => [
                'pon' => [['port_id' => 1], ['port_id' => 2], ['port_id' => 3], ['port_id' => 4]],
            ],
        ]);
    }

    private function parseOnutableScript($html)
    {
        $device = session('olt_session');
        if (!$device) {
            abort(403, 'OLT belum login.');
        }

        $data = [];
        if ($device->type === 'HIOSO 2 PON') {
            // Cari bagian array dari variabel ponOnuTable
            if (preg_match('/var\s+ponOnuTable\s*=\s*new\s+Array\s*\((.*?)\);/s', $html, $match)) {
                $raw = $match[1];
                // Hapus karakter newline dan carriage return
                $cleaned = str_replace(["\n", "\r"], '', $raw);
                // Pisahkan entri-entri berdasarkan pola
                $entries = preg_split("/',\s*'/", trim($cleaned, "'"));
                // Karena tipe HIOSO 2 PON memiliki 13 elemen per baris, chunk array dengan 13 item
                $rows = array_chunk($entries, 13);

                foreach ($rows as $row) {
                    // Pastikan jumlah elemen sesuai
                    if (count($row) < 13) {
                        continue;
                    }

                    // Mapping field sesuai dengan urutan array pada script JavaScript:
                    //   [0] OnuId, [1] Name, [2] MacAddress, [3] Status,
                    //   [4] Version, [5] ChipId, [6] PortNumber,
                    //   [7] nilai yang ditampilkan pada kolom "Temperature",
                    //   [8] dan [9] (tidak ditampilkan, bisa disimpan jika perlu),
                    //   [10] TxPower, [11] RxPower, [12] nilai mentah untuk Distance.
                    $data[] = [
                        'id' => $row[0] ?? '',
                        'name' => $row[1] ?? '',
                        'mac' => $row[2] ?? '',
                        'status' => $row[3] ?? '',
                        'fw_version' => $row[4] ?? '',
                        'chip_id' => $row[5] ?? '',
                        'ports' => $row[6] ?? '',
                        'temperature' => $row[7] ?? '',
                        'unused1' => $row[8] ?? '', // data tambahan (tidak ditampilkan)
                        'unused2' => $row[9] ?? '', // data tambahan (tidak ditampilkan)
                        'tx_power' => $row[10] ?? '',
                        'rx_power' => $row[11] ?? '',
                        'distance' => $row[12] ?? '',
                    ];
                }
            }
        } else {
            if (preg_match('/var\s+onutable\s*=\s*new\s+Array\s*\((.*?)\);/s', $html, $match)) {
                $raw = $match[1];
                $cleaned = str_replace(["\n", "\r"], '', $raw);
                $entries = preg_split("/',\s*'/", trim($cleaned, "'"));
                $rows = array_chunk($entries, 16);

                foreach ($rows as $row) {
                    if (count($row) < 16) {
                        continue;
                    }

                    $data[] = [
                        'id' => $row[0] ?? '',
                        'name' => $row[1] ?? '',
                        'mac' => $row[2] ?? '',
                        'status' => $row[3] ?? '',
                        'fw_version' => $row[4] ?? '',
                        'chip_id' => $row[5] ?? '',
                        'ports' => $row[6] ?? '',
                        'distance' => $row[7] ?? '',
                        'ctc_status' => $row[8] ?? '',
                        'ctc_ver' => $row[9] ?? '',
                        'activate' => $row[10] ?? '',
                        'temperature' => $row[11] ?? '',
                        'tx_power' => $row[14] ?? '',
                        'rx_power' => $row[15] ?? '',
                    ];
                }
            }
        }

        return $data;
    }

    public function rename(Request $request)
    {
        $device = session('olt_session');

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Belum login OLT'], 403);
        }

        $payload = [
            'onuId' => $request->onuid,
            'onuName' => $request->new_name,
            'onuOperation' => 'nonOp',
        ];

        $url = "http://{$device->host}/goform/setOnu";

        $response = Http::withBasicAuth($device->username, $device->password)
            ->withHeaders([
                'Referer' => "http://{$device->host}/onuConfig.asp?onuno={$request->input('onu_id')}&oltponno={$request->input('pon_id')}",
            ])
            ->asForm()
            ->withOptions(['verify' => false])
            ->post($url, $payload);

        if ($process->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil melakukan rename ONU.']);
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => 'Gagal melakukan rename ONU.',
                'debug' => $response->body(),
            ],
            500,
        );
    }

    public function reboot(Request $request)
    {
        $device = session('olt_session');

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Belum login OLT'], 403);
        }

        $payload = [
            'onuId' => $request->onuid,
            'onuName' => $request->old_name, // Bisa dikosongkan karena tidak rename
            'onuOperation' => 'rebootOp',
        ];

        $url = "http://{$device->host}/goform/setOnu";

        $response = Http::withBasicAuth($device->username, $device->password)
            ->withHeaders([
                'Referer' => "http://{$device->host}/onuConfig.asp?onuno={$request->input('onu_id')}&oltponno={$request->input('pon_id')}",
            ])
            ->asForm()
            ->withOptions(['verify' => false])
            ->post($url, $payload);

        if ($process->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil melakukan reboot ONU.']);
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => 'Gagal melakukan reboot ONU.',
                'debug' => $response->body(),
            ],
            500,
        );
    }

    public function delete(Request $request)
    {
        $device = session('olt_session');

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Belum login OLT'], 403);
        }

        $payload = [
            'onuId' => $request->onuid, // contoh: 0/1:5
        ];

        $url = "http://{$device->host}/goform/deleteOnu";

        $response = Http::withBasicAuth($device->username, $device->password)
            ->withHeaders([
                'Referer' => "http://{$device->host}/onuDeleteOnuList.asp?oltponno={$request->input('pon_id')}",
            ])
            ->asForm()
            ->withOptions(['verify' => false])
            ->post($url, $payload);

        if ($process->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Berhasil menghapus ONU.']);
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => 'Gagal menghapus ONU.',
                'debug' => $response->body(),
            ],
            500,
        );
    }

    public function saveConfig(Request $request)
    {
        $device = session('olt_session');

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Belum login OLT'], 403);
        }

        $url = "http://{$device->host}/goform/saveConfiguration";

        // Kirim GET request dengan query string ?savecfg=
        $response = Http::withBasicAuth($device->username, $device->password)
            ->withHeaders([
                'Referer' => "http://{$device->host}/system_save.asp",
            ])
            ->withOptions(['verify' => false, 'timeout' => 10])
            ->get($url, [
                'savecfg' => '',
            ]);

        if ($process->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Konfigurasi berhasil disimpan.']);
        }

        return response()->json(
            [
                'status' => 'error',
                'message' => 'Gagal menyimpan konfigurasi.',
                'debug' => $response->body(),
            ],
            500,
        );
    }
}
