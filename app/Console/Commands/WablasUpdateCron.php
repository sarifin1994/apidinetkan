<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WablasMessage;
use App\Models\Wablas;

class WablasUpdateCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wablas_update:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wablas Update Message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wablas = WablasMessage::where('status', 'pending')->get('id_message')->toArray();
        $id_message = array_column($wablas, 'id_message');

        $data = [
            'id_message' => $id_message,
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/get-message');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if ($result !== null) {
            $klien = $data['id_message'];
            $server = $result['data'];

            foreach ($klien as $idms) {
                if (in_array($idms, $server)) {
                    $wablas_update = WablasMessage::where('id_message', $idms);
                    $wablas_update->update([
                        'status' => 'success',
                    ]);
                } else {
                    // $wablas_update = WablasMessage::whereNot('id_message', $idms)->where('status', 'pending');
                    // $wablas_update->update([
                    //     'status' => 'pending',
                    // ]);
                }
            }
        }
    }
}
