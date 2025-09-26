<?php

namespace Modules\Prometheus\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PrometheusService
{
    private $client;
    private $prometheusUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->prometheusUrl = config('services.prometheus.url');
    }

    public function query($query)
    {
        try {
            $response = $this->client->get($this->prometheusUrl . '/api/v1/query', [
                'query' => [
                    'query' => $query
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data']['result'][0]['value'][1] ?? null;
        } catch (\Exception $e) {
            Log::error('Prometheus query failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getMetrics()
    {
        return [
            'cpu_usage' => $this->query('100 - (avg by (instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)'),
            'network_download' => $this->query('rate(node_network_receive_bytes_total[5m])'),
            'network_upload' => $this->query('rate(node_network_transmit_bytes_total[5m])'),
            'ram_total' => $this->query('node_memory_MemTotal_bytes'),
            'ram_free' => $this->query('node_memory_MemFree_bytes'),
            'memory_usage' => $this->query('node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes'),
            'memory_realtime' => $this->query('rate(node_memory_MemAvailable_bytes[5m])'),
            'disk_usage' => $this->query('100 - ((node_filesystem_free_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100)'),
            'disk_read' => $this->query('rate(node_disk_read_bytes_total[5m])'),
            'disk_write' => $this->query('rate(node_disk_written_bytes_total[5m])')
        ];
    }
}
