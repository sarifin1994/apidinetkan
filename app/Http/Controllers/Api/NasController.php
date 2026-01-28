<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Mikrotik\Nas;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class NasController extends Controller
{
    public function checkPing(Request $request)
    {
        $nas = Nas::find($request->id);

        if (!$nas) {
            return response()->json(['ping' => false]);
        }

        try {
            $client = new Client([
                'host' => $nas->ip_router,
                'user' => $nas->user,
                'pass' => $nas->password,
                'port' => $nas->port_api,
            ]);
            $query = new Query('/system/identity/print');
            $response = $client->query($query)->read();

            return response()->json(['ping' => $response ? true : false]);
        } catch (\Exception $e) {
            return response()->json(['ping' => false]);
        }
    }
}
