<?php

namespace App\Console\Commands;

use App\Enums\MemberStatusEnum;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeMember;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PascabayarCycle0Cron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pascabayar_cycle0:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice Pascabayar Billing Cycle Prorate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('id_group')->where('role', 'Admin')->get();
        foreach ($users as $user) {
            $today = Carbon::now()->format('Y-m-d');
            $initialDate = Carbon::now()->startOfMonth()->toDateString();
            $services = PppoeMember::where('group_id', $user->id_group)
                ->where([
                    ['payment_type', 'Pascabayar'],
                    ['billing_period', 'Billing Cycle'],
                    ['next_invoice', $initialDate],
                ])
                ->whereNot('tgl', '01')
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

                // Check if invoice already exists
                $invoiceExists = Invoice::where('group_id', $user->id_group)
                    ->where('member_id', $member->id)
                    ->where('pppoe_member_id', $service->id)
                    ->exists();

                if (!$invoiceExists) {
                    // Calculate dates
                    $periodeAwal = Carbon::createFromFormat('Y-m-d', $service->reg_date)->toDateString();
                    $periodeAkhir = Carbon::createFromFormat('Y-m-d', $service->reg_date)
                        ->endOfMonth()
                        ->toDateString();
                    $nextTagihan = Carbon::createFromFormat('Y-m-d', $service->next_invoice)
                        ->startOfMonth()
                        ->addMonthsNoOverflow(1)
                        ->toDateString();

                    $awal = date('d/m/Y', strtotime($periodeAwal));
                    $akhir = date('d/m/Y', strtotime($periodeAkhir));
                    $subscribe = $awal . ' s.d ' . $akhir;

                    // Calculate due date
                    $dueDateSetting = BillingSetting::where('group_id', $user->id_group)
                        ->value('due_bc');
                    $nextDue = Carbon::parse($service->next_invoice)
                        ->day($dueDateSetting)
                        ->format('Y-m-d');

                    // Prorate calculation
                    $daysInMonth = Carbon::createFromFormat('Y-m-d', $service->reg_date)->daysInMonth;
                    $endOfMonth = Carbon::createFromFormat('Y-m-d', $service->reg_date)
                        ->endOfMonth()
                        ->toDateString();
                    $usageDays = Carbon::parse($service->reg_date)->diffInDays($endOfMonth) + 1;
                    $dailyPrice = $service->profile->price / $daysInMonth;
                    $prorate = $usageDays * $dailyPrice;

                    // Prepare invoice data
                    $noInvoice = date('m') . rand(0000000, 9999999);
                    $invoice = [
                        'group_id'       => $user->id_group,
                        'pppoe_id'       => $service->pppoe_id,
                        'member_id'      => $member->id,
                        'pppoe_member_id'     => $service->id,
                        'no_invoice'     => $noInvoice,
                        'item'           => 'Internet: ' . $service->pppoe->username . ' | ' . $service->pppoe->profile . ': aktif @' . $usageDays . ' hari',
                        'price'          => $prorate,
                        'ppn'            => $service->ppn,
                        'discount'       => $service->discount,
                        'invoice_date'   => $today,
                        'due_date'       => $nextDue,
                        'period'         => $periodeAwal,
                        'subscribe'      => $subscribe,
                        'payment_type'   => $service->payment_type,
                        'billing_period' => $service->billing_period,
                        'payment_url'    => route('invoice.pay', $noInvoice),
                        'status'         => 0,
                        'created_at'     => Carbon::now(),
                    ];

                    // Handle notifications
                    $billing = BillingSetting::where('group_id', $user->id_group)
                        ->select('notif_it')
                        ->first();

                    if ($billing && $billing->notif_it === 1 && $member->wa !== null) {
                        // Format dates
                        $periodeFormat     = indonesiaDateFormat($periodeAwal);
                        $dueDateFormat     = date('d/m/Y', strtotime($nextDue));
                        $invoiceDateFormat = date('d/m/Y', strtotime($today));

                        // Calculate amounts
                        $amountPpn      = ($prorate * $service->ppn) / 100;
                        $amountDiscount = ($prorate * $service->discount) / 100;
                        $total          = $prorate + $amountPpn - $amountDiscount;

                        $amountFormat = number_format($prorate, 0, '.', '.');
                        $totalFormat  = number_format($total, 0, '.', '.');

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
                            $invoiceDateFormat,
                            $amountFormat,
                            $service->ppn,
                            $service->discount,
                            $totalFormat,
                            $periodeFormat,
                            $dueDateFormat,
                            $invoice['payment_url']
                        ];
                        $message       = str_replace($shortcode, $source, $template);
                        $messageFormat = str_replace('<br>', "\n", $message);

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
                                'message'    => $messageFormat,
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

                    // Update service's next invoice date
                    $service->update([
                        'next_invoice' => $nextTagihan,
                    ]);

                    $invoiceData[] = $invoice;
                }
            }

            // Save invoices
            Invoice::insert($invoiceData);
        }

        $count = count($invoiceData) ?? 0;

        Log::info($count . ' Invoice Pascabayar Billing Cycle Prorate executed successfully at ' . date('Y-m-d H:i:s'));
    }
}
