<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;

class RadiusLogController extends Controller
{
    public function show()
    {
        $user = 'root';
        $host = env('IP_RADIUS_SERVER');
        $remoteFile = '/var/log/radius/radius.log';
        $localFile = storage_path('app/public/radius.log');

        $command = "scp {$user}@{$host}:{$remoteFile} {$localFile}";
        exec($command);

        if (request()->ajax()) {
           
            $logPath = storage_path('app/public/radius.log');
            if (!file_exists($logPath)) {
                return response()->json([]);
            }

            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = [];

            foreach ($lines as $line) {
                if (preg_match('/^(.*?) : (\w+): (.+)$/', $line, $matches)) {
                    $logs[] = [
                        'timestamp' => $matches[1],
                        'type' => $matches[2],
                        'message' => $matches[3],
                    ];
                } else {
                    $logs[] = [
                        'timestamp' => '',
                        'type' => 'OTHER',
                        'message' => $line,
                    ];
                }
            }
            return DataTables::of($logs)->make(true);
        }
        return view('backend.radius.log.index');
    }

    public function clearLog()
    {
        $logPath = '/var/log/radius/radius.log';

        if (!file_exists($logPath)) {
            return response()->json(['status' => 'error', 'message' => 'Log file not found.'], 404);
        }

        // Hapus isi file
        file_put_contents($logPath, '');

        return response()->json(['status' => 'success', 'message' => 'Log berhasil dibersihkan.']);
    }
}
