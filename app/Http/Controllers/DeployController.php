<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deploy(Request $request)
    {
        Log::info($request->header());
//        if ($request->header('X-Deploy-Token') !== env('DEPLOY_TOKEN')) {
//            abort(403, 'Unauthorized');
//        }

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
