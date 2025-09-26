<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wablas;
use App\Models\Invoice;
use App\Models\PppoeUser;
use Illuminate\Support\Str;
use App\Models\WablasMessage;
use App\Models\BillingSetting;
use App\Models\WablasTemplate;
use App\Enums\MemberStatusEnum;
use Illuminate\Console\Command;

class InvoiceReminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice_reminder:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice Reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        function tgl_indo($tanggal)
        {
            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            $pecahkan = explode('-', $tanggal);
            return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
        }
        $users = User::select('id_group')->where('role', 'Admin')->get();
        foreach ($users as $user) {
            // notif_bi adalah notifikasi invoice reminder
            $notif_bi = BillingSetting::where('group_id', $user->id_group)
                ->select('notif_bi')
                ->first();
            $inv_reminder = $notif_bi->notif_bi;
            if ($inv_reminder !== 0) {
                $now = Carbon::now();
                $due_date = $now->addDays($inv_reminder)->format('Y-m-d');
                $today = Carbon::now()->format('Y-m-d');
                $invoices = Invoice::where('group_id', $user->id_group)
                    ->where('status', 0)->where('due_date', $due_date)
                    ->with('member')->get();
                if ($invoices !== 0) {
                    foreach ($invoices as $row) {

                        if ($row->member->status === MemberStatusEnum::INACTIVE) {
                            continue;
                        }

                        if ($row->member->wa !== null) {
                            $pppoe = PppoeUser::where('id', $row->member->pppoe_id)
                                ->select('username', 'value', 'profile')
                                ->get();
                            foreach ($pppoe as $ppp) {
                                $get_periode = date('Y-m-d', strtotime($row->period));
                                $periode_format = tgl_indo($get_periode);

                                $amount_ppn = ($row->price * $row->ppn) / 100;
                                $amount_discount = ($row->price * $row->discount) / 100;
                                $total = $row->price + $amount_ppn - $amount_discount;
                                $amount_format = number_format($row->price, 0, '.', '.');
                                $total_format = number_format($total, 0, '.', '.');

                                $due_date_format = date('d/m/Y', strtotime($row->due_date));
                                $invoice_date_format = date('d/m/Y', strtotime($row->invoice_date));

                                $template = WablasTemplate::where('id', $user->id_group)
                                    ->get('invoice_reminder')
                                    ->first()->invoice_reminder;
                                $shortcode = ['[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[paket_internet]', '[no_invoice]', '[tgl_invoice]', '[jumlah]', '[ppn]', '[discount]', '[total]', '[periode]', '[jth_tempo]', '[payment_midtrans]'];
                                $source = [$row->member->full_name, $row->member->id_member, $ppp->username, $ppp->value, $ppp->profile, $row->no_invoice, $invoice_date_format, $amount_format, $row->ppn, $row->discount, $total_format, $periode_format, $due_date_format, $row->payment_url];
                                $message = str_replace($shortcode, $source, $template);
                                $message_format = str_replace('<br>', "\n", $message);
                                $pesan = [];
                                $draw_message = [
                                    'group_id' => $user->id_group,
                                    'id_message' => Str::random(30),
                                    'subject' => 'INVOICE REMINDER #' . $row->no_invoice,
                                    'message' => preg_replace("/\r\n|\r|\n/", '<br>', $message),
                                    'phone' => $row->member->wa,
                                    'status' => 'pending',
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                ];
                                $pesan[] = $draw_message;
                                $save_pesan = WablasMessage::insert($pesan);

                                $wablas = Wablas::where('group_id', $user->id_group)
                                    ->select('token', 'sender')
                                    ->first();
                                $data = [
                                    'api_key' => $wablas->token,
                                    'sender' => $wablas->sender,
                                    'number' => $row->member->wa,
                                    'message' => $message_format,
                                    'id_message' => $draw_message['id_message'],
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

        \Log::info('Invoice reminder berhasil di jalankan ' . date('Y-m-d H:i:s'));
    }
}
