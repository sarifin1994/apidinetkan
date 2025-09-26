<?php

namespace App\Http\Controllers\Dinetkan;

use App\Http\Controllers\Controller;
use App\Models\UserDinetkan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PingIPController extends Controller
{

    public function test(){
        $ip = "103.184.122.9";
        // Jalankan perintah ping 1 kali dengan timeout 1 detik
        $pingCommand = PHP_OS_FAMILY === 'Windows' ? "ping -n 1 $ip" : "ping -c 1 $ip";
        exec($pingCommand, $output, $status);

        // Cek status kode (0 = sukses, 1 atau selain 0 = gagal)
        echo json_encode($output);
        echo "<br>";
        if ($status === 0) {
            echo 'UP';
        } else {
            echo 'DOWN';
        }
    }

    public function index()
    {

        $userdinetkan = UserDinetkan::where('is_dinetkan', 1)->get();
        foreach ($userdinetkan as $user){
            if($user->ip_prefix != "" && $user->ip_prefix != null){
                $ipexp = explode('/',$user->ip_prefix);
                // Execute the ping command based on the OS
                $ip = $ipexp[0];// "103.184.122.9";
                $rto = 0;
                $pingCommand = PHP_OS_FAMILY === 'Windows' ? "ping -n 5 $ip" : "ping -c 5 $ip";
                $output = shell_exec($pingCommand);
                $pingArrayOrig = explode("\n", trim($output)); // Ubah output ke array

                if (strpos($output, 'unreachable') !== false || empty($output)) {
                    $message = "Testing is unreachable saat ping ke ".$ip;

                    $apiUrl = "http://103.184.122.170/api/whatsapp/send-message/session1"; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                    try {
                        $params = array(
                            "jid" => "62816600661@s.whatsapp.net",
                            "content" => array(
                                "text" => $message
                            )
                        );
                        // Kirim POST request ke API eksternal
                        $response = Http::post($apiUrl, $params);

                    } catch (\Exception $e) {

                    }
                } else {
                    $pingArray = ['message' => 'The IP $ip is reachable.', 'data' => $pingArrayOrig];
                    $json = json_encode($pingArray);
                    $data = json_decode($json, true);
                    $dataLines = $data['data'];
                    $address = '';
                    $minTime = $maxTime = $avgTime = $loss = null;
                    // Ambil IP Address dari baris pertama
                    if (preg_match('/Pinging ([\d\.]+)/', $dataLines[0], $matches)) {
                        $address = $matches[1];
                    }
                    // Ambil statistik ping
                    foreach ($dataLines as $line) {
                        if (preg_match('/Minimum = (\d+)ms, Maximum = (\d+)ms, Average = (\d+)ms/', $line, $matches)) {
                            $minTime = $matches[1];
                            $maxTime = $matches[2];
                            $avgTime = $matches[3];
                        }
                        if (preg_match('/Lost = (\d+)/', $line, $matches)) {
                            $loss = $matches[1];
                        }
                        if (preg_match("/request\s+timed\s+out/i", $line)) {
                            $rto = 1;
                            // proses send to wa
                            $message = "Testing ada RTO saat ping ke ".$ip;

                            $apiUrl = "http://103.184.122.170/api/whatsapp/send-message/session1"; //env('CACTI_ENDPOINT').'cacti/logout/'.$_id;
                            try {
                                $params = array(
                                    "jid" => "62816600661@s.whatsapp.net",
                                    "content" => array(
                                        "text" => $message
                                    )
                                );
                                // Kirim POST request ke API eksternal
                                $response = Http::post($apiUrl, $params);

                            } catch (\Exception $e) {

                            }
                        }
                    }

                    // Output hasil
                    $result = [
                        'username' => $user->username,
                        'output' => $pingArrayOrig,
                        'address' => $address,
                        'min_time' => (int) $minTime,
                        'max_time' => (int) $maxTime,
                        'avg' => (int) $avgTime,
                        'loss' => (int) $loss,
                        'rto' => (int) $rto
                    ];
                }
            }
        }

        return response()->json(['message' => 'The IP '.$ip.' is reachable.', 'data' => $result]);

    }


}
