<?php

namespace App\Console\Commands;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\AdminInvoice;
use App\Models\GrafikMikrotik;
use App\Models\MasterMikrotik;
use App\Models\UserDinetkan;
use App\Models\WatemplateDinetkan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RouterOS\Client;
use RouterOS\Query;

class GetMrtgCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_mrtg_cycle:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get grafik mrtg';


    /**
     * Execute the console command.
     */
//    public function handle()
//    {
//        $mikrotik = MasterMikrotik::query()->get();
//        foreach ($mikrotik as $mrt){
//            $this->client = new Client([
//                'host' => $mrt->ip,
//                'user' => $mrt->username,
//                'pass' => $mrt->password,
//                'port' => $mrt->port, // port API Mikrotik kamu
//                'timeout' => 10,
//            ]);
//            $query = new Query('/interface/vlan/print');
//            $hasil = $this->client->query($query)->read();
//            $interface = [];
//            foreach ($hasil as $row){
//                $queryTraffic = (new Query('/interface/monitor-traffic'))
//                    ->equal('interface', $row['interface'])
//                    ->equal('once', '');
//                $trafficResult = $this->client->query($queryTraffic)->read();
//                if (isset($trafficResult[0])) {
//                    $trafficData = $trafficResult[0];
//                    GrafikMikrotik::create([
//                        'id_mikrotik' => $mrt->id,
//                        'vlan_id' => $row['.id'],
//                        'vlan_name' => $row['name'],
//                        'interface' => $row['interface'],
//                        'rx_packets_per_second' => $trafficData['rx-packets-per-second'] ?? '0',
//                        'tx_packets_per_second' => $trafficData['tx-packets-per-second'] ?? '0',
//
//                        'rx_bits_per_second' => $trafficData['rx-bits-per-second'] ?? '0',
//                        'tx_bits_per_second' => $trafficData['tx-bits-per-second'] ?? '0',
//
//                        'fp_rx_packets_per_second' => $trafficData['fp-rx-packets-per-second'] ?? '0',
//                        'fp_tx_packets_per_second' => $trafficData['fp-tx-packets-per-second'] ?? '0',
//
//                        'fp_rx_bits_per_second' => $trafficData['fp-rx-bits-per-second'] ?? '0',
//                        'fp_tx_bits_per_second' => $trafficData['fp-tx-bits-per-second'] ?? '0',
//
//                        'rx_drops_per_second' => $trafficData['rx-drops-per-second'] ?? '0',
//                        'tx_drops_per_second' => $trafficData['tx-drops-per-second'] ?? '0',
//
//                        'rx_errors_per_second' => $trafficData['rx-errors-per-second'] ?? '0',
//                        'tx_errors_per_second' => $trafficData['tx-errors-per-second'] ?? '0',
//
//                        'tx_queue_drops_per_second' => $trafficData['tx-queue-drops-per-second'] ?? '0',
//                ]);
//            } else {
//                }
//            }
//        }
//    }
    public function handle()
    {
        $mikrotikList = MasterMikrotik::query()->get();

        foreach ($mikrotikList as $mrt) {
            try {
                $this->client = new Client([
                    'host' => $mrt->ip,
                    'user' => $mrt->username,
                    'pass' => $mrt->password,
                    'port' => $mrt->port,
                    'timeout' => 10, // bisa diturunkan jadi 5 agar tidak lama menunggu
                ]);

                $query = new Query('/interface/vlan/print');
                $hasil = $this->client->query($query)->read();

                foreach ($hasil as $row) {
                    try {
                        $queryTraffic = (new Query('/interface/monitor-traffic'))
                            ->equal('interface', $row['interface'])
                            ->equal('once', '');

                        $trafficResult = $this->client->query($queryTraffic)->read();

                        if (!empty($trafficResult[0])) {
                            $trafficData = $trafficResult[0];

                            GrafikMikrotik::create([
                                'id_mikrotik' => $mrt->id,
                                'vlan_id' => $row['.id'],
                                'vlan_name' => $row['name'],
                                'interface' => $row['interface'],
                                'rx_packets_per_second' => $trafficData['rx-packets-per-second'] ?? '0',
                                'tx_packets_per_second' => $trafficData['tx-packets-per-second'] ?? '0',
                                'rx_bits_per_second' => $trafficData['rx-bits-per-second'] ?? '0',
                                'tx_bits_per_second' => $trafficData['tx-bits-per-second'] ?? '0',
                                'fp_rx_packets_per_second' => $trafficData['fp-rx-packets-per-second'] ?? '0',
                                'fp_tx_packets_per_second' => $trafficData['fp-tx-packets-per-second'] ?? '0',
                                'fp_rx_bits_per_second' => $trafficData['fp-rx-bits-per-second'] ?? '0',
                                'fp_tx_bits_per_second' => $trafficData['fp-tx-bits-per-second'] ?? '0',
                                'rx_drops_per_second' => $trafficData['rx-drops-per-second'] ?? '0',
                                'tx_drops_per_second' => $trafficData['tx-drops-per-second'] ?? '0',
                                'rx_errors_per_second' => $trafficData['rx-errors-per-second'] ?? '0',
                                'tx_errors_per_second' => $trafficData['tx-errors-per-second'] ?? '0',
                                'tx_queue_drops_per_second' => $trafficData['tx-queue-drops-per-second'] ?? '0',
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Gagal mengambil traffic dari interface {$row['interface']} Mikrotik {$mrt->ip}: " . $e->getMessage());
                    }
                }
            } catch (\RouterOS\Exceptions\StreamException $e) {
                Log::error("Tidak bisa konek ke Mikrotik {$mrt->ip} (Timeout): " . $e->getMessage());
                continue; // lanjut ke mikrotik berikutnya
            } catch (\Exception $e) {
                Log::error("Error umum saat konek ke Mikrotik {$mrt->ip}: " . $e->getMessage());
                continue;
            }
        }
    }

}
