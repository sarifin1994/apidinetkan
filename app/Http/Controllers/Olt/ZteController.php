<?php

namespace App\Http\Controllers\Olt;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Process;
use App\Library\ZteAPI;

class ZteController extends Controller
{
    public function index()
    {
        $olt = new ZteAPI();
        $connectResult = $olt->connect();

        if ($connectResult['status'] === 'error') {
            return response()->json($connectResult, 500);
        }

        $response = $olt->executeCommand('show pon power onu-rx gpon-olt_1/2/1');

        return response()->json($response);
    }
}
