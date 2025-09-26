<?php

use App\Models\CountNumbering;
use App\Models\LicenseDinetkan;
use App\Models\MappingAdons;
use App\Models\MappingUserLicense;
use App\Models\Setting\Mduitku;
use App\Models\SmtpSetting;
use App\Models\WatemplateDinetkan;
use App\Services\CustomMailerService;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Enums\ServiceStatusEnum;
use Illuminate\Http\Client\ConnectionException;

if (! function_exists('multi_auth')) {
    function multi_auth()
    {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }
        if (Auth::guard('mitra')->check()) {
            return Auth::guard('mitra')->user();
        }
        if (Auth::guard('admin_dinetkan')->check()) {
            return Auth::guard('admin_dinetkan')->user();
        }
        return null;
    }
}


if (! function_exists('build_no_invoice')) {
    function build_no_invoice($prefix = "RQ")
    {
        $no_invoice = $prefix.time();
        return $no_invoice;
    }
}

if (! function_exists('is_dedicated')) {
    function is_dedicated()
    {
        if (multi_auth()->dinetkan_user_id) {
            $mapping = \App\Models\MappingUserLicense::query()
                ->where('dinetkan_user_id', multi_auth()->dinetkan_user_id)
                ->where('status', ServiceStatusEnum::ACTIVE)
                ->get();

            $total = $mapping->count();

            if ($total === 1 && $mapping[0]->category_id != 1) {
                return true;
            } elseif ($total > 1 && ! $mapping->contains('category_id', 1)) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}

if (!function_exists('getVlansFromNms')) {
    /**
     * Get VLANs data from NMS API
     *
     * @return array|null
     */
    function getVlansFromNms(): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => env('NMS_API_TOKEN'),
            ])->get('https://nms.dinetkan.com/api/v0/resources/vlans');

            if ($response->successful()) {
                $data = $response->json();

                // Pastikan format sesuai harapan
                if (isset($data['status']) && $data['status'] === 'ok') {
                    return $data['vlans'] ?? [];
                } else {
                    \Illuminate\Support\Facades\Log::warning('NMS API response not ok', $data);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Failed to fetch VLANs: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception while fetching VLANs: ' . $e->getMessage());
        }

        return null;
    }
}

if (!function_exists('getDevicesFromNms')) {
    /**
     * Get VLANs data from NMS API
     *
     * @return array|null
     */
    function getDevicesFromNms(): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => env('NMS_API_TOKEN'),
            ])->get('https://nms.dinetkan.com/api/v0/devices');

            if ($response->successful()) {
                $data = $response->json();

                // Pastikan format sesuai harapan
                if (isset($data['status']) && $data['status'] === 'ok') {
                    return $data['devices'] ?? [];
                } else {
                    \Illuminate\Support\Facades\Log::warning('NMS API response not ok', $data);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Failed to fetch devices: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception while fetching devices: ' . $e->getMessage());
        }

        return null;
    }
}

if (!function_exists('getIfnameFromNms')) {
    /**
     * Get VLANs data from NMS API
     *
     * @return array|null
     */
    function getIfnameFromNms($hostname): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-Auth-Token' => env('NMS_API_TOKEN'),
            ])->get('https://nms.dinetkan.com/api/v0/devices/'.$hostname.'/ports');

            if ($response->successful()) {
                $data = $response->json();

                // Pastikan format sesuai harapan
                if (isset($data['status']) && $data['status'] === 'ok') {
                    return $data['ports'] ?? [];
                } else {
                    \Illuminate\Support\Facades\Log::warning('NMS API response not ok', $data);
                }
            } else {
                \Illuminate\Support\Facades\Log::error('Failed to fetch ports: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception while fetching ports: ' . $e->getMessage());
        }

        return null;
    }
}

if (!function_exists('getPortbits')) {
    /**
     * Get VLANs data from NMS API
     *
     * @return array|null
     */
    function getPortbits($hostname, $ifname)
    {
        try {
            $encodedPort = rawurlencode($ifname); // untuk path, bukan query
            $url = "https://nms.dinetkan.com/api/v0/devices/{$hostname}/ports/{$encodedPort}/port_bits";

            $response = Http::withHeaders([
                'X-Auth-Token' => env('NMS_API_TOKEN'),
            ])->get($url);

            // Kirim ulang responsenya sebagai gambar
            return Response::make($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type'),
            ]);

//            $encoded = urlencode($ifname);
//            $response = Http::withHeaders([
//                'X-Auth-Token' => env('NMS_API_TOKEN'),
//            ])->get('https://nms.dinetkan.com/api/v0/devices/'.$hostname.'/ports/'.$encoded.'/port_bits');
//
//            if ($response->successful()) {
//                $data = $response->json();
//
//                // Pastikan format sesuai harapan
//                if (isset($data['status']) && $data['status'] === 'ok') {
//                    return $data['ports'] ?? [];
//                } else {
//                    \Illuminate\Support\Facades\Log::warning('NMS API response not ok', $data);
//                }
//            } else {
//                \Illuminate\Support\Facades\Log::error('Failed to fetch ports: ' . $response->body());
//            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception while fetching ports: ' . $e->getMessage());
        }

        return null;
    }
}

if (! function_exists('gantiformat_hp')) {
    function gantiformat_hp($nomorhp) {
        //Terlebih dahulu kita trim dl
        $nomorhp = trim($nomorhp);
        //bersihkan dari karakter yang tidak perlu
        $nomorhp = strip_tags($nomorhp);
        // Berishkan dari spasi
        $nomorhp= str_replace(" ","",$nomorhp);
        // bersihkan dari bentuk seperti  (022) 66677788
        $nomorhp= str_replace("(","",$nomorhp);
        // bersihkan dari format yang ada titik seperti 0811.222.333.4
        $nomorhp= str_replace(".","",$nomorhp);

        //cek apakah mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/',trim($nomorhp))){
            // cek apakah no hp karakter 1-3 adalah +62
            if(substr(trim($nomorhp), 0, 2)=='62'){
                $nomorhp= trim($nomorhp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr($nomorhp, 0, 1)=='0'){
                $nomorhp= '62'.substr($nomorhp, 1);
            }
        }
        return $nomorhp;
    }
}
if (!function_exists('hitungProrate')) {
    function hitungProrate($jenisPembayaran, $tanggalAktif, $tanggalBayar, $license_id, $prorata = 'on') {

        $license = LicenseDinetkan::query()
            ->where('id', $license_id)
            ->first();
        $hargaBulanan = $license->price;
        $aktif = \Carbon\Carbon::parse($tanggalAktif);
        $bayar = \Carbon\Carbon::parse($tanggalBayar);

        $hariDalamBulan = $aktif->daysInMonth;
        $mulai = null;
        $akhir = null;

        if ($jenisPembayaran === 'prabayar') {
            $mulai = $aktif;
            $akhir = $bayar;
        } elseif ($jenisPembayaran === 'pascabayar') {
            $mulai = $aktif;
            $akhir = $bayar;//$bayar->copy()->addMonthNoOverflow();
        }

        $jumlahHari = $mulai->diffInDays($akhir);
        if($jumlahHari < 0){
            $jumlahHari = $jumlahHari * -1;
        }
        $prorate = ($jumlahHari / $hariDalamBulan) * $hargaBulanan;

        if($prorata == 'off'){
            return [
                'success' => true,
                'mulai' => $mulai->toDateString(),
                'akhir' => $akhir->toDateString(),
                'hari_pakai' => $jumlahHari,
                'harga_asli' => $hargaBulanan,
                'harga_prorate' => $hargaBulanan,
                'ppn' => $license->ppn,
                'message' => ''
            ];
        }
        return [
            'success' => true,
            'mulai' => $mulai->toDateString(),
            'akhir' => $akhir->toDateString(),
            'hari_pakai' => $jumlahHari,
            'harga_asli' => $hargaBulanan,
            'harga_prorate' => round($prorate),
            'ppn' => $license->ppn,
            'message' => ''
        ];
//        return round($prorate);
    }
}

function formatBytes($bytes, $precision = 2): null|string
{
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

    if ($bytes == 0) {
        return null;
    }

    $i = (int) floor(log($bytes) / log(1024));

    return $i == 0 ? $bytes . ' ' . $sizes[$i] : round($bytes / (1024 ** $i), $precision) . ' ' . $sizes[$i];
}

function formatTime($seconds): string
{
    $time = '';

    $days = floor($seconds / 86400);
    $seconds %= 86400;
    $hours = floor($seconds / 3600);
    $seconds %= 3600;
    $minutes = floor($seconds / 60);
    $seconds %= 60;

    if ($days > 0) {
        $time .= $days . 'd ';
    }

    if ($hours > 0) {
        $time .= $hours . 'h ';
    }

    if ($minutes > 0) {
        $time .= $minutes . 'm ';
    }

    return $time;
}

function indonesiaDateFormat(string $date): string
{
    $months = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $parts = explode('-', $date);
    $monthIndex = (int)$parts[1];
    $year = $parts[0];

    return $months[$monthIndex] . ' ' . $year;
}

function convertToVPort($id)
{
    $BASE_ID = 402718976;
    $INCREMENT = 65536; // 1 << 16

    [$onuId, $vportId] = explode('.', $id);
    return (int) (($vportId - $BASE_ID) / $INCREMENT) + 1;
}

function convertToVPort2($id)
{
    // Base ID untuk type 2
    $BASE_ID_TYPE_2 = 503316480; // Sesuaikan dengan nilai yang benar
    $INCREMENT = 65536; // 1 << 16

    // Pisahkan ID berdasarkan titik
    [$onuId, $portId] = explode('.', $id);

    // Hitung VPort ID berdasarkan BASE_ID dan INCREMENT
    return (int) (($portId - $BASE_ID_TYPE_2) / $INCREMENT) + 1;
}

/**
 * Reverse VPort Number to Service Port ID
 *
 * @param int $onuId ONU ID
 * @param int $vportNo VPort Number
 * @return string Service Port ID
 */
function reverseToId($onuId, $vportNo)
{
    $BASE_ID = 402718976;
    $INCREMENT = 65536; // 1 << 16

    $vportId = $BASE_ID + (($vportNo - 1) * $INCREMENT);
    return "{$onuId}.{$vportId}";
}

function subnetMaskToPrefix($subnetMask)
{
    $octets = explode('.', $subnetMask);
    $binaryString = '';

    foreach ($octets as $octet) {
        // Ubah setiap oktet ke biner 8 bit
        $binaryString .= str_pad(decbin($octet), 8, '0', STR_PAD_LEFT);
    }

    // Hitung jumlah bit '1' (itu prefix length)
    $prefixLength = substr_count($binaryString, '1');

    return $prefixLength;
}

function reverseToIdV2($onuId, $vportNo)
{
    $BASE_ID = 268500992; // Base ID untuk versi 2 (ONU vport sub-interface)
    $INCREMENT = 65536; // 1 << 16
    $OFFSET = 256; // Koreksi nilai agar sesuai

    $vportId = $BASE_ID + (($vportNo - 1) * $INCREMENT) + $OFFSET;
    return "{$onuId}.{$vportId}";
}

function calculateBoundId($onuId, $vPort)
{
    // Step 1: Convert ONU ID to binary (32 bits for ONU ID)
    $onuIdBinary = sprintf("%032b", $onuId); // 32 bits for ONU ID

    // Step 2: Convert VPort to binary (11 bits for VPort number)
    $vPortBinary = sprintf("%011b", $vPort); // 11 bits for VPort number

    // Step 3: Combine ONU ID and VPort into a 43-bit binary representation
    $combinedBinary = $onuIdBinary . $vPortBinary;

    // Step 4: Convert the combined binary string into a decimal number
    $subIndex = bindec($combinedBinary);

    // Step 5: Construct the result as a floating-point number
    $formattedSubIndex = $onuId . '.' . $subIndex;
    return $subIndex;
}

function parseVlanHex($hexString)
{
    $vlans = [];
    $length = strlen($hexString);

    for ($i = 0; $i < $length; $i += 4) {
        $hexPart = substr($hexString, $i, 4);
        $vlanId = hexdec($hexPart);

        if ($vlanId === 0) {
            break;
        }

        $vlans[] = $vlanId;
    }

    return $vlans;
}

// Reverse array VLAN ID ke HEX
function vlanArrayToHex(array $vlans)
{
    $hexString = '';

    foreach ($vlans as $vlanId) {
        $hexString .= str_pad(dechex($vlanId), 4, '0', STR_PAD_LEFT);
    }

    $hexString .= '0000';
    $hexString = str_pad($hexString, 48, '0');

    return strtoupper($hexString);
}

function convertVlansToHexOctet(array $vlans): string
{
    $maxSize = 24; // total byte
    $result = '';

    foreach ($vlans as $vlan) {
        // Konversi VLAN ID ke 2 byte hex
        $result .= pack('n', $vlan);
    }

    // Tambahkan penanda akhir VLAN (00 00)
    $result .= pack('n', 0);

    // Hitung sisa byte untuk memenuhi SIZE(24)
    $paddingLength = $maxSize - strlen($result);

    if ($paddingLength > 0) {
        $result .= str_repeat("\x00", $paddingLength);
    }

    return bin2hex($result); // Ubah hasilnya ke hex string
}

function makeRequest($url, $method="GET", $params = []){
    try {
        $data = null;
        $response = null;
        if($method == "GET"){
            $response = Http::get($url, $params);
            return $response->json();
        }
        if($method == "POST"){
            $response = Http::post($url, $params);
            return $response->json();
        }
        if($method == "DELETE"){
            $response = Http::delete($url);
        }
        if($method == "PATCH"){
            $response = Http::patch($url, $params);
        }
        if($method == "PUT"){
            $response = Http::put($url, $params);
        }
        if ($response->successful()) {
            $data = $response->json();
        }
        return $data;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function save_wa_log($shortname,$receiver,$message,$status = "PENDING"){
    \App\Models\Whatsapp\WhatsappLog::create([
        'shortname' => $shortname,
        'send_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'receiver' => $receiver,
        'message' => $message,
        'status' => $status
    ]);
}

function get_panduan($kode){
    $panduan = [
        'VA' => [
            "Via ATM Maybank" => [
                "Masukkan kartu ATM & PIN.",
                "Pilih Pembayaran > Virtual Account.",
                "Masukkan nomor Virtual Account.",
                "Periksa nominal & nama penerima.",
                "Konfirmasi dan selesaikan transaksi."
            ],
            "Via M2U ID App" => [
                "Login ke M2U ID.",
                "Pilih Bayar Tagihan > Virtual Account.",
                "Masukkan nomor VA.",
                "Konfirmasi data, lalu bayar."
            ]
        ],
        'BT' => [
            "Via ATM Permata" => [
                "Masukkan kartu dan PIN.",
                "Pilih Transaksi Lainnya > Pembayaran > Virtual Account.",
                "Masukkan nomor VA.",
                "Konfirmasi dan bayar."
            ],
            "Via PermataMobile X" => [
                "Login ke aplikasi.",
                "Pilih Bayar > Virtual Account.",
                "Masukkan nomor VA.",
                "Lanjutkan pembayaran."
            ]
        ],
        'B1' => [
            "Via ATM CIMB Niaga" => [
                "Masukkan kartu dan PIN.",
                "Pilih Bayar/Beli > Pembayaran Lainnya > Virtual Account.",
                "Masukkan nomor VA.",
                "Periksa detail dan konfirmasi."
            ],
            "Via OCTO Mobile" => [
                "Login ke OCTO Mobile.",
                "Pilih Pembayaran > Virtual Account.",
                "Input nomor VA dan konfirmasi pembayaran."
            ]
        ],
        'A1' => [
            "Via ATM Bank Apapun (Jaringan ATM Bersama)" => [
                "Masukkan kartu & PIN.",
                "Pilih Transfer > Ke Bank Lain.",
                "Masukkan kode bank VA + nomor VA.",
                "Masukkan jumlah sesuai tagihan.",
                "Konfirmasi data dan bayar.",
                "Contoh: Jika nomor VA: 1234567890 (Bank BNI, kode 009), maka input: 0091234567890"
            ]
        ],
        'I1' => [
            "Via ATM BNI" => [
                "Masukkan kartu dan PIN.",
                "Pilih Menu Lain > Pembayaran > Pembayaran Lain > Virtual Account.",
                "Masukkan nomor VA.",
                "Cek informasi & bayar."
            ],
                "Via BNI Mobile Banking" => [
                "Login ke aplikasi.",
                "Pilih Pembayaran > Virtual Account Billing.",
                "Input nomor VA.",
                "Verifikasi dan selesaikan transaksi."
            ]
        ],
        'M1' => [
            "Via ATM Mandiri" => [
                "Masukkan kartu dan PIN.",
                "Pilih Bayar/Beli > Multi Payment.",
                "Masukkan kode perusahaan + nomor VA.",
                "Konfirmasi & bayar."
            ],
                "Via Livin' by Mandiri" => [
                "Login ke Livin’.",
                "Pilih Bayar > Virtual Account.",
                "Masukkan nomor VA.",
                "Periksa nama dan jumlah > Bayar."
            ]
        ],
        'M2' => [
            "Via ATM Mandiri" => [
                "Masukkan kartu dan PIN.",
                "Pilih Bayar/Beli > Multi Payment.",
                "Masukkan kode perusahaan + nomor VA.",
                "Konfirmasi & bayar."
            ],
            "Via Livin' by Mandiri" => [
                "Login ke Livin’.",
                "Pilih Bayar > Virtual Account.",
                "Masukkan nomor VA.",
                "Periksa nama dan jumlah > Bayar."
            ]
        ],
        'AG' => [
            "Via ATM Artha Graha" => [
                "Masukkan kartu & PIN.",
                "Pilih Pembayaran > Virtual Account.",
                "Masukkan nomor VA.",
                "Konfirmasi detail, lalu bayar."
            ]
        ],
        'BR' => [
            "Via ATM BRI" => [
                "Masukkan kartu & PIN.",
                "Pilih Transaksi Lain > Pembayaran > Lainnya > BRIVA.",
                "Masukkan nomor VA.",
                "Konfirmasi dan bayar."
            ],
                "Via BRImo" => [
                "Login ke BRImo.",
                "Pilih BRIVA.",
                "Masukkan nomor VA.",
                "Cek data dan bayar."
            ]
        ],
        'NC' => [
            "Via Neobank App" => [
                "Login ke aplikasi BNC (Neobank).",
                "Pilih Transfer > Bayar Virtual Account.",
                "Masukkan nomor VA.",
                "Lanjutkan proses pembayaran."
            ]
        ],
        'BV' => [
            "Via ATM BSI" => [
                "Masukkan kartu & PIN.",
                "Pilih Bayar > Virtual Account.",
                "Masukkan nomor VA.",
                "Konfirmasi dan bayar."
            ],
                "Via BSI Mobile" => [
                "Login ke aplikasi.",
                "Pilih Bayar > Virtual Account.",
                "Masukkan nomor VA.",
                "Periksa detail dan lanjutkan pembayaran."
            ]
        ],
        'BC' => [
            "Via ATM BCA" => [
                "Masukkan kartu & PIN.",
                "Pilih Transaksi Lainnya > Transfer > Ke Rekening Virtual Account.",
                "Masukkan nomor VA.",
                "Konfirmasi data dan bayar."
            ],
            "Via BCA Mobile" => [
                "Login ke m-BCA.",
                "Pilih m-Transfer > BCA Virtual Account.",
                "Masukkan nomor VA > Send.",
                "Konfirmasi dan bayar."
            ]
        ]
    ];
    if(isset($panduan[$kode])){
        return $panduan[$kode];
    } else {
        return [];
    }
}

//function get_vlan_mikrotik(){
//    $url = env('API_MIKROTIK')."interface/vlan/print";
//    $response = Http::timeout(10)->get($url);
//    $response = $response->json();
//    if(isset($response['data'])){
//        return $response['data'];
//    }
//    return [];
//}

function get_vlan_mikrotik()
{
    $url = env('API_MIKROTIK') . "interface/vlan/print";

    try {
        $response = Http::timeout(5) // tambah waktu timeout
        ->retry(3, 2000) // coba ulang 3x dengan jeda 2 detik
        ->get($url);

        if ($response->successful()) {
            $json = $response->json();
            if (isset($json['data'])) {
                return $json['data'];
            }
        }

        return []; // kalau tidak ada data
    } catch (ConnectionException $e) {
        // Log error supaya bisa ditrace
        \Log::error("Mikrotik API timeout: " . $e->getMessage());
        return [];
    } catch (\Exception $e) {
        \Log::error("Mikrotik API error: " . $e->getMessage());
        return [];
    }
}


function cacti_logout(){
    //        http://103.184.122.170/api/cacti/logout/:_id
    $_id = Str::lower(Str::replace(' ', '', auth()->user()->name));
    $apiUrl = env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
    try {
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->get($apiUrl);
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data['success'] ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}



function cacti_login(){
    $_id = Str::lower(Str::replace(' ', '', auth()->user()->name));
    $apiUrl = env('CACTI_ENDPOINT').'cacti/login/'.$_id;
    try {
        $params = array(
            "action" =>"login",
            "login_username" => "wijaya",
            "login_password" => "wijaya@2024"
        );
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->post($apiUrl, $params);
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data['success'] ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

function get_tree_mrtg(){
    try {
        $params = array(
            "action" =>"get_node",
            "tree_id" => "0",
            "id" => "%23"
        );
        $_id = Str::lower(Str::replace(' ', '', auth()->user()->name));
        $apiUrl = env('CACTI_ENDPOINT').'cacti/graph_view/'.$_id.'?' . urldecode(http_build_query($params)) ;
        // Kirim POST request ke API eksternal
        $response = Http::timeout(10)->get($apiUrl);
        // Periksa apakah request berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data ?? null;
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}
    function get_payment_method()
    {
        $duitku = Mduitku::where('shortname', 'dinetkan')->first();
        // Set kode merchant anda
        $merchantCode = $duitku->id_merchant;
        // Set merchant key anda
        $apiKey = $duitku->api_key;
        // catatan: environtment untuk sandbox dan passport berbeda

        $datetime = date('Y-m-d H:i:s');
        $paymentAmount = 10000;
        $signature = hash('sha256', $merchantCode . $paymentAmount . $datetime . $apiKey);

        $params = array(
            'merchantcode' => $merchantCode,
            'amount' => $paymentAmount,
            'datetime' => $datetime,
            'signature' => $signature
        );
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        if (env('APP_ENV') == 'production') {
            $url = 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';
        }
        $response = makeRequest($url, "POST", $params);
        $data = $response;
        $paymentMethod = [];
        $paymentMethod[] = 'Select Payment';
        if (isset($data['paymentFee'])) {
            $filteredVA = collect($data['paymentFee'])->filter(function ($item) {
                return Str::contains($item['paymentName'], ' VA');
            });

            // Contoh: ambil hanya paymentName-nya
            $paymentNames = $filteredVA->pluck('paymentName', 'paymentMethod');

            // Tampilkan
            foreach ($paymentNames as $key => $val) {
                $paymentMethod[$key] = $val;
            };
            return $paymentMethod;
        } else {
            return [];
        }
    }

    function generateMemberIdPPPOE(): string
    {
        // Get the latest member ID or start from 0
        $lastMember = CountNumbering::where('tipe', 'pppoe_user')->first();
        if($lastMember == null){
            $lastMember = CountNumbering::create([
                'tipe' => 'pppoe_user',
                'count' => 0
            ]);
        }
        $lastNumber = $lastMember ? (int)$lastMember->count : 0;
        $nextNumber = $lastNumber + 1;

        // Format to 8 digits with leading zeros

        $lastMember->count = $nextNumber;
        $lastMember->save();
        $pid = multi_auth()->id;//str_pad(multi_auth()->id, 6, '0', STR_PAD_LEFT);
        $userid = \Illuminate\Support\Carbon::now()->format('Ym').str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $pid.$userid;
    }

    function bank_list($key=""){
        $bankList = [
            '002' => 'Bank BRI',
            '008' => 'Bank Mandiri',
            '009' => 'Bank BNI',
            '011' => 'Bank Danamon',
            '013' => 'Bank Permata',
            '014' => 'Bank Central Asia',
            '016' => 'Bank Maybank Indonesia',
            '019' => 'Bank Panin',
            '022' => 'CIMB Niaga',
            '023' => 'Bank UOB Indonesia',
            '028' => 'Bank OCBC NISP',
            '031' => 'Citi Bank',
            '036' => 'Bank CCB (Ex-Bank Windu Kentjana)',
            '037' => 'Bank Artha Graha',
            '042' => 'MUFG Bank',
            '046' => 'Bank DBS',
            '050' => 'Standard Chartered Bank',
            '054' => 'Bank Capital',
            '061' => 'ANZ Indonesia',
            '069' => 'Bank Of China Indonesia',
            '076' => 'Bank Bumi Arta',
            '087' => 'Bank HSBC Indonesia',
            '095' => 'Bank JTrust Indonesia',
            '097' => 'Bank Mayapada',
            '110' => 'Bank BJB',
            '111' => 'Bank DKI',
            '112' => 'Bank BPD DIY',
            '113' => 'Bank Jateng',
            '114' => 'Bank Jatim',
            '115' => 'Bank Jambi',
            '116' => 'Bank Aceh',
            '117' => 'Bank Sumut',
            '118' => 'Bank Nagari',
            '119' => 'Bank Riau Kepri',
            '120' => 'Bank Sumsel Babel',
            '121' => 'Bank Lampung',
            '122' => 'Bank Kalsel',
            '123' => 'Bank Kalbar',
            '124' => 'Bank Kaltimtara',
            '125' => 'Bank Kalteng',
            '126' => 'Bank Sulselbar',
            '127' => 'Bank Sulut Go',
            '128' => 'Bank NTB Syariah',
            '129' => 'Bank BPD Bali',
            '130' => 'Bank NTT',
            '131' => 'Bank Maluku Malut',
            '132' => 'Bank Papua',
            '133' => 'Bank Bengkulu',
            '134' => 'Bank Sulteng',
            '135' => 'Bank Sultra',
            '137' => 'Bank Banten',
            '146' => 'Bank Of India Indonesia',
            '147' => 'Bank Muamalat Indonesia',
            '151' => 'Bank Mestika',
            '152' => 'Bank Shinhan Indonesia',
            '153' => 'Bank Sinarmas',
            '157' => 'Bank Maspion Indonesia',
            '161' => 'Bank Ganesha',
            '164' => 'Bank ICBC Indonesia',
            '167' => 'Bank QNB Indonesia',
            '200' => 'Bank BTN',
            '212' => 'Bank Woori Saudara',
            '213' => 'Bank BTPN',
            '405' => 'Bank Victoria Syariah',
            '425' => 'Bank BJB Syariah',
            '426' => 'Bank Mega',
            '441' => 'Bank KB Bukopin',
            '451' => 'Bank Syariah Indonesia',
            '459' => 'Bank KROOM',
            '472' => 'Bank Jasa Jakarta',
            '484' => 'Bank KEB Hana',
            '485' => 'MNC Bank',
            '490' => 'Bank Neo Commerce',
            '494' => 'Bank BRI Agroniaga',
            '498' => 'Bank SBI',
            '501' => 'Bank Digital BCA',
            '503' => 'Bank Nobu',
            '506' => 'Bank Mega Syariah',
            '513' => 'Bank Ina Perdana',
            '517' => 'Bank Panin Dubai Syariah',
            '520' => 'Bank Prima Master',
            '521' => 'Bank Syariah Bukopin',
            '523' => 'Bank Sahabat Sampoerna',
            '526' => 'Bank Oke Indonesia',
            '531' => 'AMAR BANK',
            '535' => 'SEA Bank',
            '536' => 'Bank BCA Syariah',
            '542' => 'Bank Jago',
            '547' => 'Bank BTPN Syariah',
            '548' => 'Bank Multiarta Sentosa',
            '553' => 'Bank Mayora',
            '555' => 'Bank Index Selindo',
            '562' => 'Superbank (FAMA)',
            '564' => 'Bank Mantap',
            '566' => 'Bank Victoria International',
            '567' => 'Allo Bank',
            '600' => 'BPR SUPRA',
            '688' => 'BPR KS',
            '699' => 'BPR EKA',
            '789' => 'IMkas',
            '911' => 'LinkAja',
            '945' => 'Bank Agris',
            '947' => 'Bank Aladin Syariah',
            '949' => 'Bank CTBC',
            '950' => 'Bank Commonwealth',
            '1010' => 'OVO',
            '1011' => 'Gopay',
            '1012' => 'DANA',
            '1013' => 'Shopeepay',
            '1014' => 'LinkAja Direct',
        ];
        if($key){
            return $bankList[$key];
        }
        return $bankList;
    }

    function send_faktur_inv($no_invoice,  SiteDinetkanSettings $settings, $_template = 'terbit'){
        try{
            $smtp = SmtpSetting::firstWhere('shortname', 'dinetkan');
            $invoice = \App\Models\AdminDinetkanInvoice::query()->where('no_invoice', $no_invoice)->first();
            if($invoice){
//                $priceData = $adminPaymentService->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
                $user = \App\Models\UserDinetkan::query()->where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
                $mapping = MappingUserLicense::where('id', $invoice->id_mapping)->first();
                $adons = MappingAdons::where('id_mapping', $mapping->id)->get();

                $total_ppn_ad = 0;
                $logoPath = public_path('assets/images/dinetkan_logo.png');
                $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf_v2',
                    compact(
                        'invoice',
                        'settings',
                        'adons',
                        'total_ppn_ad',
                        'logoPath'
                    ))->setPaper('a4', 'potrait');

                // Simpan ke storage sementara
                $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
                $pdf->save($pdfPath);

                $placeholders = [
                    '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
                    '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
                    '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
                ];
                $total = $invoice->price + $invoice->total_ppn + $invoice->price_adon;
                $values = [
                    $user->name,
                    $mapping->service_id,
                    $user->username,
                    "",
                    $user->address,
                    $invoice->item,
                    $invoice->payment_type,
                    $invoice->billing_period,
                    $invoice->no_invoice,
                    Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'),
                    number_format($invoice->price, 0, ',', '.'),
                    $invoice->ppn,
                    $invoice->discount,
                    number_format($total, 0, ',', '.'),
                    Carbon::parse($invoice->due_date)->translatedFormat('d F Y'),
                    Carbon::parse($invoice->due_date)->translatedFormat('F Y'),
                    $invoice->subscribe,
                    $invoice->payment_url,
                ];


                $watemplate = WatemplateDinetkan::firstWhere('shortname', 'dinetkan');
                $template = $watemplate->invoice_terbit;
                $subject = 'Invoice Terbit';
                if($_template == 'lunas'){
                    $template = $watemplate->payment_paid;
                    $subject = 'Invoice Lunas';
                }
                $message_orig = str_replace($placeholders, $values, $template);
                $message = str_replace('<br>', "\n", $message_orig);

                $data = [
                    'messages' => $message_orig,
                    'user_name' => $user->username,
                    'notification' => 'Informasi Invoice',
                    'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
                ];
                app(CustomMailerService::class)->sendWithUserSmtpCron(
                    'emails.generate_invoice',
                    $data,
                    $user->email,
                    $subject,
                    $smtp,
                    $pdfPath
                );
                Log::error("Invoice berhasil dikirim dengan template => ".$_template);
            }
            return null;
        }catch (\Exception $e){
            Log::error("[invoice:reminder-notification] Exception sending email to " . $e->getMessage());
        }
    }


