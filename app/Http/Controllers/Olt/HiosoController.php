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
            // return back()->with('error', 'Login gagal: ' . $e->getMessage());
            $api = new HiosoAPI($request->id);
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
                    $allOnu = []; // Ensure this is initialized before the loop
                    // Define the two PON ports (e.g., "1" and "2")
                    $ponIndexes = ['1', '2'];

                    foreach ($ponIndexes as $pon) {
                        $url = 'http://' . $device['host'] . '/onuConfigOnuList.asp?oltponno=0/1/' . $pon;
                        try {
                            $response = Http::withBasicAuth($device['username'], $device['password'])
                                ->withOptions(['verify' => false])
                                ->get($url);
                            // dd($response->body());

                            if ($response->successful()) {
                                $html = $response->body();
                                $parsed = $this->parseData($html);
                                $allOnu = array_merge($allOnu, $parsed);
                            }
                        } catch (\Exception $e) {
                            // Optionally, you can log this error or handle it as needed per port
                        }
                    }

                    // Calculate aggregate data after processing all PON ports
                    $countTotal = count($allOnu);
                    $countUp = 0;
                    $countDown0 = 0;
                    $countDown1 = 0;

                    foreach ($allOnu as $onu) {
                        if ($onu['status'] === 'Up') {
                            $countUp++;
                        } elseif ($onu['status'] === 'Down') {
                            // Depending on your logic, adjust this counter accordingly.
                            $countDown0++;
                        }elseif ($onu['status'] === 'PwrDown') {
                            // Depending on your logic, adjust this counter accordingly.
                            $countDown1++;
                        }
                    }

                    return DataTables::of(collect($allOnu))
                        ->addIndexColumn()
                        ->with([
                            'aggregate' => [
                                'countTotal' => $countTotal,
                                'countUp' => $countUp,
                                'countDown0' => $countDown0,
                                'countDown1' => $countDown1,
                            ],
                        ])
                        ->toJson();
                } else {
                    $url = 'http://' . $device['host'] . '/onuAllPonOnuList.asp';
                    try {
                        $response = Http::withBasicAuth($device['username'], $device['password'])
                            ->withOptions(['verify' => false])
                            ->get($url);

                        if ($response->successful()) {
                            $html = $response->body();
                            $parsed = $this->parseData($html);

                            $allOnu = array_merge($allOnu, $parsed);
                            // Hitung nilai agregat dari seluruh data array
                            $countTotal = count($allOnu);
                            $countUp = 0;
                            $countDown0 = 0;
                            $countDown1 = 0;

                            foreach ($allOnu as $onu) {
                                if ($onu['status'] === 'Up') {
                                    $countUp++;
                                } elseif ($onu['status'] === 'Down') {
                                    if ($onu['dying_gasp'] == 1) {
                                        $countDown1++;
                                    } elseif ($onu['dying_gasp'] == 0) {
                                        $countDown0++;
                                    }
                                }
                            }
                        }
                        return DataTables::of(collect($allOnu))
                            ->addIndexColumn()
                            ->with([
                                'aggregate' => [
                                    'countTotal' => $countTotal,
                                    'countUp' => $countUp,
                                    'countDown0' => $countDown0,
                                    'countDown1' => $countDown1,
                                ],
                            ])
                            ->toJson();
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

    /**
     * Mengonversi detik ke format waktu sederhana (H M S)
     *
     * @param int $sec
     * @return string
     */
    private function sec2timeSimple($sec)
    {
        $sec = intval($sec);
        $min = floor($sec / 60);
        $seconds = $sec % 60;
        $hours = floor($min / 60);
        $min = $min % 60;

        if ($hours > 0) {
            return sprintf('%dH %dM %dS', $hours, $min, $seconds);
        } elseif ($min > 0) {
            return sprintf('%dM %dS', $min, $seconds);
        }

        return sprintf('%dS', $seconds);
    }

    /**
     * Mengambil data dari script onutable dalam HTML dan mengembalikannya sebagai array asosiatif.
     *
     * Data onutable diasumsikan memiliki 22 elemen per baris dengan urutan:
     *   0: Id,
     *   1: Name,
     *   2: MacAddress,
     *   3: Status,
     *   4: FwVersion,
     *   5: ChipId,
     *   6: Ports,
     *   7: CtcStatus (angka untuk mapping),
     *   8: CtcVer,
     *   9: Auth (jika '2' maka "Deactivate", selain itu "Activate"),
     *  10: RTT,
     *  11: Temperature,
     *  12: unused1 (data tambahan),
     *  13: unused2 (data tambahan),
     *  14: TxPower,
     *  15: RxPower,
     *  16: OnlineTime,
     *  17: OfflineTime,
     *  18: OfflineReason code (atau nilai mentah),
     *  19: Online Seconds (yang akan dikonversi ke format H M S),
     *  20: Deregister Count,
     *  21: Dying_gasp flag (jika '1' maka override offline reason menjadi "Dying_gasp")
     *
     * @param string $html
     * @return array
     */
    private function parseData($html)
    {
        $device = session('olt_session');
        $data = [];

        if ($device->type === 'HIOSO 2 PON') {
            if (preg_match('/var\s+ponOnuTable\s*=\s*new\s+Array\s*\((.*?)\);/s', $html, $match)) {
                $raw = $match[1];
                // Hapus newline dan carriage return
                $cleaned = str_replace(["\n", "\r"], '', $raw);
                // Pisahkan entri berdasarkan pola "'," dan hapus tanda kutip awal/akhir
                $entries = preg_split("/',\s*'/", trim($cleaned, "'"));
                // Pecah array menjadi baris, masing-masing 13 elemen
                $rows = array_chunk($entries, 13);

                foreach ($rows as $row) {
                    if (count($row) < 13) {
                        continue;
                    }
                    // Mapping field untuk HIOSO 2 PON
                    $record = [
                        'id' => ltrim($row[0], " '"),
                        'name' => $row[1],
                        'mac' => $row[2],
                        'status' => $row[3],
                        'fw_version' => $row[4],
                        'chip_id' => $row[5],
                        'ports' => $row[6],
                        'temperature' => $row[7],
                        // Index 8 dan 9 tidak dipakai (bisa ditambahkan jika diperlukan)
                        'tx_power' => $row[10],
                        'rx_power' => $row[11],
                        'distance_raw' => $row[12],
                    ];

                    // Contoh perhitungan distance: gunakan rumus konversi tertentu
                    $dist = floatval($record['distance_raw']) * 1.6393;
                    $record['distance'] = $dist > 157 ? round($dist - 157) : '1';

                    $data[] = $record;
                }
            }
            
            return $data; // ✅ Pasti return array
        } else {
            // Cari bagian array dari variabel onutable dengan 22 elemen per baris
            if (preg_match('/var\s+onutable\s*=\s*new\s+Array\s*\((.*?)\);/s', $html, $match)) {
                $raw = $match[1];
                // Hapus newline dan carriage return
                $cleaned = str_replace(["\n", "\r"], '', $raw);
                // Pisahkan entri berdasarkan pola "'," dan hapus tanda kutip awal/akhir
                $entries = preg_split("/',\s*'/", trim($cleaned, "'"));
                // Pecah array menjadi baris, masing-masing 22 elemen
                $rows = array_chunk($entries, 22);

                // Mapping untuk offline reason jika nilainya kurang dari 6
                $off_reason = ['Other', 'TIMEOUT', 'ONU-init', 'OLT-init', 'RejectByBlackList', 'RejectByWhiteList'];

                foreach ($rows as $row) {
                    if (count($row) < 22) {
                        continue;
                    }

                    // Tentukan offline reason
                    if ($row[21] == '1') {
                        $offline_reason = 'Dying_gasp';
                    } else {
                        if (is_numeric($row[18]) && intval($row[18]) < 6) {
                            $offline_reason = $off_reason[intval($row[18])];
                        } else {
                            $offline_reason = $row[18];
                        }
                    }

                    // Konversi auth: jika field index 9 sama dengan '2', maka "Deactivate", selain itu "Activate"
                    $auth = $row[9] == '2' ? 'Deactivate' : 'Activate';

                    // Konversi online seconds ke format waktu sederhana
                    $online_time_formatted = $this->sec2timeSimple($row[19]);

                    $data[] = [
                        'id' => $row[0],
                        'name' => $row[1],
                        'mac' => $row[2],
                        'status' => $row[3],
                        'fw_version' => $row[4],
                        'chip_id' => $row[5],
                        'ports' => $row[6],
                        'ctc_status' => $row[7],
                        'ctc_ver' => $row[8],
                        'auth' => $auth,
                        'rtt' => $row[10],
                        'temperature' => $row[11],
                        'unused1' => $row[12],
                        'unused2' => $row[13],
                        'tx_power' => $row[14],
                        'rx_power' => $row[15],
                        'online_time' => $row[16],
                        'offline_time' => $row[17],
                        'offline_reason' => $offline_reason,
                        'online_seconds' => $online_time_formatted,
                        'deregister_count' => $row[20],
                        'dying_gasp' => $row[21],
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

        if ($response->successful()) {
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

        if ($response->successful()) {
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

        if ($response->successful()) {
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

        if ($response->successful()) {
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
