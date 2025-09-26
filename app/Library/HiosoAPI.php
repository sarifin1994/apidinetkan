<?php

namespace App\Library;
use Illuminate\Support\Facades\Session;
use App\Models\Olt\OltDevice;
use Illuminate\Support\Facades\Http;

class HiosoAPI
{
    protected $oltDevice;
    protected $sessionKey = 'olt_session';

    public function __construct($id = null)
    {
        if ($id) {
            $this->login($id);
        } elseif (Session::has($this->sessionKey)) {
            $this->oltDevice = Session::get($this->sessionKey);
        } else {
            throw new \Exception('OLT device not found in session.');
        }
    }

    public function login($id)
    {
        $device = OltDevice::find($id);
        if (!$device) {
            // Coba koneksi untuk validasi login
            $url = 'http://' . $device->host . '/system.asp';
            $response = Http::withBasicAuth($device->username, $device->password)
                ->withOptions(['verify' => false])
                ->get($url);
            // throw new \Exception('OLT device not found.');
        }

        // Coba koneksi untuk validasi login
        $url = 'http://' . $device->host . '/system.asp';
        $response = Http::withBasicAuth($device->username, $device->password)
            ->withOptions(['verify' => false])
            ->get($url);

        if (!$response->successful()) {
            $url = 'http://' . $device->host . '/system.asp';
            $response = Http::withBasicAuth($device->username, $device->password)
                ->withOptions(['verify' => false])
                ->get($url);
            throw new \Exception('Login to OLT failed.');
        }

        // Simpan ke session
        Session::put($this->sessionKey, $device);
        $this->oltDevice = $device;
    }

    public function getSystemInfo(): array
    {
        $url = 'http://' . $this->oltDevice->host . '/system.asp'; // Ganti sesuai path OLT

        $response = Http::withBasicAuth($this->oltDevice->username, $this->oltDevice->password)
            ->withOptions(['verify' => false])
            ->get($url);

        if (!$response->successful()) {
            $response = Http::withBasicAuth($this->oltDevice->username, $this->oltDevice->password)
            ->withOptions(['verify' => false])
            ->get($url);
            // throw new \Exception('Failed to retrieve system info.');
        }

        return $this->parseSystemInfo($response->body());
    }

    protected function parseSystemInfo(string $html): array
    {
        // Regex fleksibel: cocok untuk new_array atau new Array
        if (!preg_match('/new(?:_array| Array)\((.*?)\);/is', $html, $match)) {
            return [];
        }

        $raw = $match[1];

        // Pisahkan dan bersihkan kutip
        $items = array_map(function ($item) {
            return trim($item, " \t\n\r\0\x0B\"'");
        }, explode(',', $raw));

        return [
            'name' => $items[0] ?? '-',
            'location' => $items[1] ?? '-',
            'description' => $items[2] ?? '-',
            'model' => $items[3] ?? '-',
            'software' => $items[4] ?? '-',
            'revision_date' => $items[5] ?? '-',
            'mac' => $items[6] ?? '-',
            'ip_address' => $items[7] ?? '-',
            'uptime' => $items[8] ?? '-',
            'hardware' => $items[9] ?? '-',
            'serial_number' => $items[10] ?? '-',
            'olt_id' => $items[11] ?? '-',
            'temperature' => $items[12] ?? '-',
            'running_time' => $items[13] ?? '-',
        ];
    }

    // public function getOltDevice()
    // {
    //     return $this->oltDevice;
    // }

    // public function getPonOverview()
    // {
    //     if (!$this->oltDevice) {
    //         throw new \Exception('OLT device not initialized.');
    //     }

    //     // Akses URL halaman overview PON
    //     if($this->oltDevice->type === 'HIOSO 2 PON'){
    //         $url = 'http://' . $this->oltDevice->host . '/onuConfigPonList.asp';
    //     }else{
    //         $url = 'http://' . $this->oltDevice->host . '/onuOverviewPonList.asp';
    //     }

    //     $response = Http::withBasicAuth($this->oltDevice->username, $this->oltDevice->password)
    //         ->withOptions(['verify' => false])
    //         ->get($url);

    //     if (!$response->successful()) {
    //         throw new \Exception('Failed to fetch PON overview.');
    //     }

    //     return $response->body(); // HTML content yang nanti akan kamu parse
    // }

    // public function parsePonList($html)
    // {
    //     // Ambil isi array dari JavaScript ponListTable
    //     preg_match('/var\s+ponListTable\s*=\s*new\s+Array\s*\((.*?)\);/s', $html, $matches);

    //     if (empty($matches[1])) {
    //         return [['error' => 'ponListTable not found']];
    //     }

    //     $content = $matches[1];

    //     // Ambil pasangan 'key','value'
    //     preg_match_all("/'([^']+)'\s*,\s*'([^']+)'/", $content, $pairs, PREG_SET_ORDER);

    //     $ponData = [];

    //     foreach ($pairs as $pair) {
    //         $ponId = trim($pair[1]);
    //         $infoStr = trim($pair[2]);

    //         $total = $online = $offline = 0;

    //         if (preg_match('/Total=(\d+),Online=(\d+),Offline=(\d+)/', $infoStr, $m)) {
    //             $total = (int) $m[1];
    //             $online = (int) $m[2];
    //             $offline = (int) $m[3];
    //         }

    //         $ponData[] = [
    //             'pon_id' => $ponId,
    //             'total' => $total,
    //             'online' => $online,
    //             'offline' => $offline,
    //         ];
    //     }

    //     return $ponData;
    // }
}
