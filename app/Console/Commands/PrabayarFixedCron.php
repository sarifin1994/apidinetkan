<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use App\Enums\MemberStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class PrabayarFixedCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prabayar_fixed:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice Prabayar Fixed Date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('id_group')->where('role', 'Admin')->get();
        foreach ($users as $user) {
            $now   = Carbon::now();
            $today = $now->format('Y-m-d');

            $inv_fd = BillingSetting::where('group_id', $user->id_group)
                ->value('inv_fd');

            $next_invoice = $now->copy()->addDays($inv_fd)->format('Y-m-d');

            // Fetch services instead of members
            $services = PppoeMember::where('group_id', $user->id_group)
                ->where([
                    ['payment_type', 'Prabayar'],
                    ['next_invoice', $next_invoice],
                ])
                ->with([
                    'pppoe:id,username,value,profile',
                    'profile:id,price',
                    'member',
                ])
                ->get();

            $invoiceData = [];

            foreach ($services as $service) {
                $member = $service->member;

                if ($member->status === MemberStatusEnum::INACTIVE) {
                    continue;
                }

                // Calculate periods
                $periode_awal  = Carbon::createFromFormat('Y-m-d', $service->next_invoice)
                    ->toDateString();
                $periode_akhir = Carbon::createFromFormat('Y-m-d', $service->next_invoice)
                    ->addMonthNoOverflow(1)
                    ->toDateString();

                $awal      = date('d/m/Y', strtotime($periode_awal));
                $akhir     = date('d/m/Y', strtotime($periode_akhir));
                $subscribe = $awal . ' s.d ' . $akhir;

                // Prepare invoice data
                $no_invoice = date('m') . rand(1000000, 9999999);
                $invoice    = [
                    'group_id'       => $user->id_group,
                    'pppoe_id'       => $service->pppoe_id,
                    'member_id'      => $member->id,
                    'pppoe_member_id'     => $service->id,
                    'no_invoice'     => $no_invoice,
                    'item'           => 'Internet: ' . $service->pppoe->username . ' | ' . $service->pppoe->profile,
                    'price'          => $service->profile->price,
                    'ppn'            => $service->ppn,
                    'discount'       => $service->discount,
                    'invoice_date'   => $today,
                    'due_date'       => $service->next_invoice,
                    'period'         => $periode_akhir,
                    'subscribe'      => $subscribe,
                    'payment_type'   => $service->payment_type,
                    'billing_period' => $service->billing_period,
                    'payment_url'    => route('invoice.pay', $no_invoice),
                    'status'         => 0,
                    'created_at'     => Carbon::now(),
                ];

                // Handle notifications
                $billing = BillingSetting::where('group_id', $user->id_group)
                    ->value('notif_it');

                if ($billing === 1 && $member->wa !== null) {
                    $get_periode          = date('Y-m-d', strtotime($service->next_invoice));
                    $periode_format       = indonesiaDateFormat($get_periode);

                    $amount_ppn           = ($service->profile->price * $service->ppn) / 100;
                    $amount_discount      = ($service->profile->price * $service->discount) / 100;
                    $total                = $service->profile->price + $amount_ppn - $amount_discount;

                    $amount_format        = number_format($service->profile->price, 0, '.', '.');
                    $total_format         = number_format($total, 0, '.', '.');
                    $due_date_format      = date('d/m/Y', strtotime($service->next_invoice));
                    $invoice_date_format  = date('d/m/Y', strtotime($today));

                    // Get message template
                    $template = WablasTemplate::where('group_id', $user->id_group)
                        ->value('invoice_terbit');

                    // Prepare message
                    $shortcode = [
                        '[nama_lengkap]',
                        '[id_pelanggan]',
                        '[username]',
                        '[password]',
                        '[paket_internet]',
                        '[no_invoice]',
                        '[tgl_invoice]',
                        '[jumlah]',
                        '[ppn]',
                        '[discount]',
                        '[total]',
                        '[periode]',
                        '[jth_tempo]',
                        '[payment_midtrans]'
                    ];
                    $source = [
                        $member->full_name,
                        $member->id_member,
                        $service->pppoe->username,
                        $service->pppoe->value,
                        $service->pppoe->profile,
                        $invoice['no_invoice'],
                        $invoice_date_format,
                        $amount_format,
                        $service->ppn,
                        $service->discount,
                        $total_format,
                        $periode_format,
                        $due_date_format,
                        $invoice['payment_url']
                    ];
                    $message        = str_replace($shortcode, $source, $template);
                    $message_format = str_replace("<br>", "\n", $message);

                    // Save message to WablasMessage
                    $messageId   = Str::random(30);
                    $messageData = [
                        'group_id'   => $user->id_group,
                        'id_message' => $messageId,
                        'subject'    => 'INVOICE TERBIT #' . $invoice['no_invoice'],
                        'message'    => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                        'phone'      => $member->wa,
                        'status'     => 'pending',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    WablasMessage::insert([$messageData]);

                    // Send message via Wablas API
                    $wablas = Wablas::where('group_id', $user->id_group)
                        ->select('token', 'sender')
                        ->first();

                    if ($wablas) {
                        $data = [
                            'api_key'    => $wablas->token,
                            'sender'     => $wablas->sender,
                            'number'     => $member->wa,
                            'message'    => $message_format,
                            'id_message' => $messageId,
                        ];
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($curl, CURLOPT_URL, config('services.whatsapp.url') . '/send-message');
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                        $response = curl_exec($curl);
                        curl_close($curl);
                    }
                }

                // Update service's next_invoice date
                $service->update([
                    'next_invoice' => $periode_akhir,
                ]);

                $invoiceData[] = $invoice;
            }

            // Save invoices
            if (!empty($invoiceData)) {
                Invoice::insert($invoiceData);
            }

            $count = count($invoiceData) ?? 0;
        }
        Log::info($count . ' Invoice Prabayar Fixed Date berhasil di jalankan pada ' . date('Y-m-d H:i:s'));
    }
}
