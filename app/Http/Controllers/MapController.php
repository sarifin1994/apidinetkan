<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class MapController extends Controller
{
    public function index()
    {
        $routers = $this->loadRouters();
        return view('map', ['routers' => $routers]);
    }

    public function getRouters()
    {
        return response()->json($this->loadRouters());
    }

    private function loadRouters()
    {
        $routers = [
            [
                'id' => 1,
                'name' => 'Router A',
                'ip_address' => '192.168.1.1',
                'username' => 'admin',
                'password' => 'mypass123',
                'port' => 1322,
                'latitude' => -6.223500,
                'longitude' => 107.130000,
                'parent_id' => null,
            ],
            [
                'id' => 2,
                'name' => 'Router B',
                'ip_address' => '38.211.24.253',
                'username' => 'radiusqu123',
                'password' => 'radiusqu123',
                'port' => 1322,
                'latitude' => -6.243000,
                'longitude' => 107.115000,
                'parent_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Router C',
                'ip_address' => '192.168.1.3',
                'username' => 'admin',
                'password' => 'mypass123',
                'port' => 1322,
                'latitude' => -6.230000,
                'longitude' => 107.105000,
                'parent_id' => 1,
            ],
        ];

        foreach ($routers as &$router) {
            $router['status'] = $this->checkRouter($router);
        }

        return $routers;
    }

    private function checkRouter($router)
    {
        try {
            $client = new Client([
                'host' => $router['ip_address'],
                'user' => $router['username'],
                'pass' => $router['password'],
                'port' => $router['port'],
                'timeout' => 2,
            ]);

            $query = new Query('/system/resource/print');
            $response = $client->query($query)->read();

            if (!empty($response)) {
                $info = $response[0];
                return [
                    'status' => 'online',
                    'cpu_load' => $info['cpu-load'] ?? 'N/A',
                    'free_memory' => isset($info['free-memory']) ? round($info['free-memory']/1024/1024, 2) . ' MB' : 'N/A',
                    'uptime' => $info['uptime'] ?? 'N/A',
                ];
            }
            return ['status' => 'offline'];
        } catch (\Exception $e) {
            return ['status' => 'offline'];
        }
    }
}
