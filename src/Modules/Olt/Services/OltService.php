<?php

namespace Modules\Olt\Services;

use App\Enums\OltDeviceEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OltService
{
    protected string $baseUrl;
    protected string $host;
    protected OltDeviceEnum $model = OltDeviceEnum::HSGQ;
    protected ?string $username;
    protected ?string $password;
    protected ?string $token;
    protected ?string $udp_port;
    protected ?string $snmp_read_write;
    protected ?string $version;

    public function __construct(OltDeviceEnum $model, string $host = '', ?string $username = '', ?string $password = '', ?string $token = '', ?string $udp_port = '', ?string $snmp_read_write = '', ?string $version = '')
    {
        if ($model->value != 'zte' && $model->value != 'fiberhome') {
            $this->baseUrl = env('OLT_API_URL');
        } else {
            $this->baseUrl = env('OLT_API_URL_ZTE_FIBERHOME');
        }

        $this->model = $model;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;
        $this->udp_port = $udp_port;
        $this->snmp_read_write = $snmp_read_write;
        $this->version = $version;
    }

    protected function makeRequest(string $endpoint, string $method = 'POST', array $data = [], array $additionalHeaders = [])
    {
        if ($this->model->value != 'zte' && $this->model->value != 'fiberhome') {
            $defaultData = array_merge([
                'model' => $this->model->value,
                'host' => $this->host,
                'username' => $this->username,
                'password' => $this->password,
                'token' => $this->token,
            ], $data);
        } else {
            $dataArray = array(
                'target' => $this->host,
                'community' => $this->snmp_read_write,
                'type' => $this->model->value . '/' . $this->version,
            );

            if ($this->udp_port !== null && $this->udp_port != '') {
                $dataArray['options'] = array('port' => $this->udp_port);
            }
            $defaultData = array_merge($dataArray, $data);
        }

        $headers = array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $additionalHeaders);

        if ($this->model->value == 'zte' || $this->model->value == 'fiberhome') {
            if ($endpoint == '/snmp/token') {
            } else {
                $headers['Authorization'] = 'Bearer ' . $this->token;
                if ($endpoint != '/snmp/token') {
                    $method = 'GET';
                    if (isset($defaultData['id_device']) || isset($defaultData['id_device_vport'])) {
                        if (isset($defaultData['id_device'])) {
                            $endpoint = '/snmp/zxAnGponSrvOnuMgmtTable/' . $defaultData['id_device'];
                        } else if (isset($defaultData['id_device_vport'])) {
                            $endpoint = '/snmp/zxAnVlanTable/' . $defaultData['id_device_vport'];
                        }
                    } else {
                        $defaultData = [];
                    }
                }
            }
        }

        try {
            $response = $this->executeRequest($method, $endpoint, $defaultData, $headers);
            if ($response->successful()) {
                return $response->json();
            }

            // Auto-login attempt for non-login endpoints
            if (($endpoint !== '/olt/login' || $endpoint !== '/snmp/token') && $this->handleAutoLogin()) {
                $defaultData['token'] = $this->token;
                $response = $this->executeRequest($method, $endpoint, $defaultData, $headers);

                if ($response->successful()) {
                    return $response->json();
                }
            }

            Log::error('OLT API Request Failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('OLT API Request Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
            return $e;
        }
    }

    protected function executeRequest($method, $endpoint, $data, $headers)
    {
        return Http::withHeaders($headers)->{$method}($this->baseUrl . $endpoint, $data);
    }

    protected function handleAutoLogin()
    {
        Log::info('Request failed, attempting to login first');
        $loginResponse = $this->login();

        if ($loginResponse && isset($loginResponse['token'])) {
            $this->setToken($loginResponse['token']);
            return true;
        }
        return false;
    }

    public function login()
    {
        if ($this->model->value != 'zte' && $this->model->value != 'fiberhome') {
            return $this->makeRequest('/olt/login');
        } else {
            return $this->makeRequest('/snmp/token');
        }
    }

    public function logout()
    {
        return $this->makeRequest('/olt/logout');
    }

    public function getBoardInfo()
    {
        $cacheKey = "olt_{$this->host}_board_info";
        if ($this->model->value != 'zte' && $this->model->value != 'fiberhome') {
            return $this->makeRequest('/olt/board-info');
        } else {
            // if($this->model->value == 'zte') {
            $data_olt = ["response" => []];
            $data_management_onu =  $this->makeRequest('/snmp/zxAnGponSrvOnuMgmtTable');
            if (empty($data_management_onu)) {
                return $data_olt;
            } else {
                $data_olt['response']['management_onu'] = $data_management_onu;
                $data_unconfigured_onu =  $this->makeRequest('/snmp/zxAnGponSrvUnConfOnuTable');
                $data_status_onu =  $this->makeRequest('/snmp/table/zxAnGponSrvOnuStatusTable');
                $data_rtx_onu =  $this->makeRequest('/snmp/table/zxAnPonRxOpticalPowerTable');
                $data_unconfigured_onu_type =  $this->makeRequest('/snmp/zxAnPonSrvChannelTable');
                $data_voip = $this->makeRequest('/snmp/zxAnGponRmVoipConfTable');
                $data_onu = $this->makeRequest('/snmp/ifXTable');
                $data_online_status = $this->makeRequest('/snmp/zxAnGponSrvOnuLastOnlineTime');
                $vlan = $this->makeRequest('/snmp/zxAnVlanIfConfTable'); 
                $wave = $this->makeRequest('/snmp/zxAnGponSrvOnuDistanceTable'); 
                $wan = $this->makeRequest('/snmp/zxAnGponRmWanIpConfTable'); 
                $upload = $this->makeRequest('/snmp/zxAnGponSrvTcontTable');  
                $download = $this->makeRequest('/snmp/zxAnGponSrvGemPortTable');  
                $data_olt['response']['onu_status'] = $data_status_onu;
                $data_olt['response']['unconfigured_onu'] = $data_unconfigured_onu;
                $data_olt['response']['rtx_onu'] = $data_rtx_onu;
                $data_olt['response']['data_unconfigured_onu_type'] = $data_unconfigured_onu_type;
                $data_olt['response']['data_voip'] = $data_voip;
                $data_olt['response']['data_onu'] = $data_onu;
                $data_olt['response']['data_online_status'] = $data_online_status;
                $data_olt['response']['vlan'] = $vlan;
                $data_olt['response']['wave'] = $wave;
                $data_olt['response']['wan'] = $wan;
                $data_olt['response']['upload'] = $upload;
                $data_olt['response']['download'] = $download;
                return $data_olt;
            }

            // } else {

            // }

        }
    }

    public function ifExntry() {
        $data_olt = ["response" => []];
            $data_management_onu =  $this->makeRequest('/snmp/ifXEntry');
            if (empty($data_management_onu)) {
                return $data_olt;
            } else {
                $data_olt['response']['management_onu'] = $data_management_onu; 
                return $data_olt;
            }
    }

    public function changeName($port, $onu, $name)
    {
        return $this->makeRequest('/olt/change-name', 'POST', [
            'port_id' => $port,
            'onu_id' => $onu,
            'name' => $name,
        ]);
    }

    public function rebootOnu($port, $onu)
    {
        return $this->makeRequest('/olt/reboot-onu', 'POST', [
            'port_id' => $port,
            'onu_id' => $onu,
        ]);
    }

    public function getOnuList($port)
    {
        $cacheKey = "olt_{$this->host}_port_{$port}_onu_list";

        return cache()->remember($cacheKey, 600, function () use ($port) {
            return $this->makeRequest('/olt/onu-list', 'POST', [
                'port_id' => $port,
            ]);
        });
    }

    public function getOnuZTEFIBER($port)
    {
        $data_olt = ["response" => []];

        $data = $this->makeRequest('/snmp/zxAnGponSrvOnuMgmtTable/', 'GET', [
            'id_device' => $port,
        ]);
        $data_olt['response']['management_onu'] = $data;
        return $data_olt;
    }
    

    public function getOnuTable()
    {
        return $this->makeRequest('/olt/onu-table');
    }

    public function getOnuData($port, $onu)
    {
        return $this->makeRequest('/olt/onu-data', 'POST', [
            'port_id' => $port,
            'onu_id' => $onu,
        ]);
    }

    public function deleteOnuList($port, $onu, $mac_address)
    {
        return $this->makeRequest('/olt/delete-onu', 'POST', [
            'port_id' => $port,
            'onu_id' => $onu,
            'mac_addr' => $mac_address,
        ]);
    }

    public function getSystemInfo()
    {
        return $this->makeRequest('/olt/system-info');
    }

    public function getInfoTime()
    {
        return $this->makeRequest('/olt/time-info');
    }

    public function rebootSystem()
    {
        return $this->makeRequest('/olt/reboot-system');
    }

    public function saveSystem()
    {
        return $this->makeRequest('/olt/save-system');
    }

    function getVLANInfo()
    {
        return $this->makeRequest('/snmp/zxAnVlanTable');
    }

    function getVlanBind($id)
    {
        $data = $this->makeRequest('/snmp/zxAnVlanTable/', 'GET', [
            'id_device_vport' => $id,
        ]);

        return $data;
    }

    function getVLANPortInfo()
    {
        return $this->makeRequest('/snmp/zxAnVlanIfConfTable');
    }

    function getOnuType()
    {
        return $this->makeRequest('/snmp/zxAnPonOnuTypeTable');
    }

    function ifTable()
    {
        return $this->makeRequest('/snmp/ifXTable');
    }

    function getTConfType()
    {
        return $this->makeRequest('/snmp/zxAnGponSrvTcontTable');
    }

    function getGemPort()
    {
        return $this->makeRequest('/snmp/zxAnGponSrvGemPortTable');
    }

    function getBandWidth()
    {
        return $this->makeRequest('/snmp/zxAnGponSrvBandwidthPrfTable');
    }

    function getTraffic()
    {
        return $this->makeRequest('/snmp/zxAnGponSrvTrafficPrfTable');
    }

    function getVlanProfile()
    {
        return $this->makeRequest('/snmp/zxAnGponRmVlanPrfTable');
    }

    function getIFEntry()
    {
        return $this->makeRequest('/snmp/ifXTable');
    }

    public function setModel(OltDeviceEnum $model)
    {
        $this->model = $model;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }
}
