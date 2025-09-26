<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wablas;
use App\Models\RadiusNas;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use App\Enums\MemberStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;


class SuspendFixedCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend_fixed:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend Fixed Date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('id_group')->where('role', 'Admin')->get();

        foreach ($users as $user) {
            $suspend_date = BillingSetting::where('group_id', $user->id_group)
                ->value('suspend_date');

            if ($suspend_date !== 0) {
                $now = Carbon::now();
                $today = $now->format('Y-m-d');
                $next_due = $now->subDays($suspend_date)->format('Y-m-d');

                // Fetch services instead of members
                $services = PppoeMember::where('group_id', $user->id_group)
                    ->where([
                        ['billing_period', 'Billing Cycle'],
                        ['next_due', $next_due],
                    ])
                    ->with([
                        'pppoe:id,username,value,profile,nas',
                        'profile:id,price',
                        'member',
                        'invoiceforsuspend',
                    ])
                    ->get();

                if ($services->count() > 0) {
                    foreach ($services as $service) {
                        $member = $service->member;

                        if ($member->status === MemberStatusEnum::INACTIVE) {
                            continue;
                        }

                        $draw = [
                            'username' => $service->pppoe->username,
                            'nas'      => $service->pppoe->nas,
                        ];

                        if ($service->pppoe->nas !== null) {
                            $nas_secret = RadiusNas::where('nasname', $service->pppoe->nas)
                                ->value('secret');

                            $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$draw['nas']}:3799 disconnect $nas_secret");
                        } else {
                            $nas_list = RadiusNas::where('group_id', $user->id_group)
                                ->select('nasname', 'secret')
                                ->get();

                            foreach ($nas_list as $item) {
                                $command = Process::path('/usr/bin/')->run("echo User-Name='{$draw['username']}' | radclient -r 1 {$item->nasname}:3799 disconnect {$item->secret}");
                            }
                        }

                        // Update service status to suspended
                        $service->update([
                            'status' => 2, // Assuming 2 means suspended
                        ]);

                        $billing = BillingSetting::where('group_id', $user->id_group)
                            ->value('notif_sm');

                        if ($billing === 1 && $member->wa !== null) {
                            $template = WablasTemplate::where('group_id', $user->id_group)
                                ->value('invoice_overdue');

                            $invoice = $service->invoiceforsuspend->first();
                            if ($invoice) {
                                $amount_ppn      = ($invoice->price * $invoice->ppn) / 100;
                                $amount_discount = ($invoice->price * $invoice->discount) / 100;
                                $total           = $invoice->price + $amount_ppn - $amount_discount;

                                $amount_format       = number_format($invoice->price, 0, '.', '.');
                                $total_format        = number_format($total, 0, ',', '.');
                                $due_date_format     = date('d/m/Y', strtotime($invoice->due_date));
                                $invoice_date_format = date('d/m/Y', strtotime($invoice->invoice_date));

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
                                    '[payment_midtrans]',
                                ];
                                $source = [
                                    $member->full_name,
                                    $member->id_member,
                                    $service->pppoe->username,
                                    $service->pppoe->value,
                                    $service->pppoe->profile,
                                    $invoice->no_invoice,
                                    $invoice_date_format,
                                    $amount_format,
                                    $invoice->ppn,
                                    $invoice->discount,
                                    $total_format,
                                    $invoice->subscribe,
                                    $due_date_format,
                                    $invoice->payment_url,
                                ];
                                $message        = str_replace($shortcode, $source, $template);
                                $message_format = str_replace('<br>', "\n", $message);

                                // Save message to WablasMessage
                                $messageId   = Str::random(30);
                                $messageData = [
                                    'group_id'   => $user->id_group,
                                    'id_message' => $messageId,
                                    'subject'    => 'INVOICE OVERDUE #' . $invoice->no_invoice,
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
                        }
                    }
                }
            }
        }

        Log::info('Suspend cycle has been successfully executed at ' . date('Y-m-d H:i:s'));
    }
}
