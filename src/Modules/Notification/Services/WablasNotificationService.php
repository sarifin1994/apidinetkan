<?php

namespace Modules\Notification\Services;

use App\Models\Wablas;

final class WablasNotificationService
{
    public function sendWablasMessage(string $phone, string $message, int $group_id): void
    {
        $wablas = Wablas::where('group_id', $group_id)->select('token', 'sender')->first();
        if (!$wablas) {
            return;
        }

        $data = [
            'api_key' => $wablas->token,
            'sender'  => $wablas->sender,
            'number'  => $phone,
            'message' => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);
        curl_close($ch);
    }
}
