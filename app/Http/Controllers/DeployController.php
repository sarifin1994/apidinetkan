<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        Log::info($request->all());
        $signature = $request->header('X-Hub-Signature-256');
        $payload   = $request->getContent();
        $secret    = env('DEPLOY_TOKEN');

        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            abort(403, 'Invalid signature');
        }

        \exec('/www/wwwroot/api-dev.dinetkan.com/deploy.sh');

        return response()->json([
            'status' => 'ok',
            'message' => 'Auto deploy success'
        ]);
    }

    public function check(){
        return response()->json([
            'status' => 'ok',
            'message' => 'Test OK LAGI.'
        ]);
    }
}
