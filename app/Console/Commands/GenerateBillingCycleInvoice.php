<?php

namespace App\Console\Commands;

use App\Models\MappingAdons;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Invoice\Invoice;

class GenerateBillingCycleInvoice extends Command
{
    protected $signature = 'invoice:billing-cycle';
    protected $description = 'Generate invoices for Billing Cycle (run on the 1st of every month)';

    public function handle()
    {
        // Set timezone for consistency
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');

        $today = Carbon::today();
        // Only run on the first day of month
        // if (! $today->isSameDay($today->copy()->startOfMonth())) {
        //     Log::info('[invoice:billing-cycle] Skipping, not first of month');
        //     return 0;
        // }

        // Process users in batches
        User::where('role', 'Admin')
            ->whereIn('status', [1, 3])
            ->with('c_pppoe_cycle.c_profile')
            ->chunkById(50, function ($users) use ($today) {
                foreach ($users as $user) {
                    $this->processUser($user, $today);
                }
            });

        return 0;
    }

    protected function processUser($user, Carbon $today)
    {
        $shortname = $user->shortname;
        $domain = $user->domain;

        // Load settings and templates
        $billingSetting = BillingSetting::firstWhere('shortname', $shortname);
        $mpwaConfig     = Mpwa::firstWhere('shortname', $shortname);
        $waTemplate     = Watemplate::firstWhere('shortname', $shortname);

        if (! $billingSetting || ! $waTemplate) {
            Log::error("[BillingCycle][{$shortname}] Missing billingSetting or watemplate");
            return;
        }

        $notifIt = (bool) $billingSetting->notif_it;
        $dueDay  = (int)  $billingSetting->due_bc;
        $template = $waTemplate->invoice_terbit;

        foreach ($user->c_pppoe_cycle as $pppoe) {
            // Conditions: Pascabayar + Billing Cycle + no existing invoice this month
            if ($pppoe->payment_type !== 'Pascabayar'
                || $pppoe->billing_period !== 'Billing Cycle'
                || $this->hasInvoiceThisMonth($pppoe)) {
                continue;
            }

            $data = $this->prepareCycleData($pppoe, $dueDay, $today);
            if (! $data) {
                continue;
            }

            // Create invoice record
            $invoice = Invoice::create([
                'shortname'     => $shortname,
                'id_pelanggan'  => $pppoe->id,
                'no_invoice'    => $data['no_invoice'],
                'item'          => $data['item'],
                'price'         => $data['price'],
                'ppn'           => $pppoe->ppn,
                'discount'      => $pppoe->discount,
                'invoice_date'  => $today->toDateString(),
                'due_date'      => $data['due_date'],
                'period'        => $data['period_start'],
                'subscribe'     => $data['subscribe'],
                'payment_type'  => $pppoe->payment_type,
                'billing_period'=> $pppoe->billing_period,
                'payment_url'   => $domain . '/pay/' . $data['no_invoice'],
                'status'        => 'unpaid',
                'mitra_id'      => $pppoe->mitra_id,
                'komisi'        => $pppoe->c_profile->fee_mitra,
            ]);

            if (! $invoice) {
                Log::error("[BillingCycle][{$shortname}] Failed creating invoice for {$pppoe->username}");
                continue;
            }

            $cekmapping = MappingAdons::query()->where('id_pelanggan_pppoe', $pppoe->id_pelanggan)->where('monthly', 'Yes') ->get();
            if(count($cekmapping) > 0){
                $total_price_ad = 0;
                $total_price_ad_monthly=0;
                foreach ($cekmapping as $mapp)
                    $mappingadons = MappingAdons::create(
                        [
                            'id_mapping' => 0,
                            'description' => $mapp->description,
                            'ppn' => $mapp->ppn,
                            'monthly' => $mapp->monthly,
                            'qty' => $mapp->qty,
                            'price' => $mapp->price,
                            'no_invoice' => $mapp->no_invoice,
                            'id_pelanggan_pppoe' => $mapp->id_pelanggan_pppoe
                        ]);
                $totalPpnAd = 0;
                if($mapp->ppn > 0){
                    $totalPpnAd = $mapp->ppn * ($mapp->qty * $mapp->price) / 100;
                }
                $total_price_ad = $total_price_ad + (($mapp->qty * $mapp->price) + $totalPpnAd);

                if($mapp->monthly == "Yes"){
                    $total_price_ad_monthly = $total_price_ad_monthly + (($mapp->qty * $mapp->price) + $totalPpnAd);
                }
                $invoice->update([
                    'price_adon_monthly' => $total_price_ad_monthly,
                    'price_adon' => $total_price_ad
                ]);
            }

            // Update next_invoice and due on PPPoE
            $pppoe->update([
                'next_invoice' => $data['next_invoice'],
                'due_date'     => $data['due_date'],
            ]);

            // Send WA notification if enabled
            if ($notifIt && ! empty($pppoe->wa)) {
                $this->sendWaNotification($pppoe, $invoice, $template, $mpwaConfig, $shortname);
            }

            Log::info("[BillingCycle][{$shortname}] Invoice generated for {$pppoe->username}");
            sleep(5); // throttle per invoice
        }
    }

    protected function hasInvoiceThisMonth($pppoe): bool
    {
        return Invoice::where('shortname', $pppoe->shortname)
            ->where('id_pelanggan', $pppoe->id)
            ->whereYear('due_date', now()->year)
            ->whereMonth('due_date', now()->month)
            ->exists();
    }
    protected function hasPreviousInvoice($pppoe)
    {
        return Invoice::where('id_pelanggan', $pppoe->id)->exists();
    }

    protected function prepareCycleData($pppoe, int $dueDay, Carbon $today): ?array
    {
        // Tentukan apakah sudah ada invoice sebelumnya
        $hasPreviousInvoice = $this->hasPreviousInvoice($pppoe);

        // Determine proration if registration day not 1
        $regDay = Carbon::parse($pppoe->reg_date);

        if (!$hasPreviousInvoice) {
            // Belum ada invoice → pakai tanggal registrasi
            $periodStart = $regDay->copy();
            $periodEnd = $periodStart->copy()->endOfMonth();
            $totalDays = $periodStart->diffInDays($periodEnd) + 1; // hitung hari, termasuk hari terakhir
            $item = "Internet: {$pppoe->id_pelanggan} | {$pppoe->c_profile->name} aktif @$totalDays hari";
        } else {
            // Ada invoice → pakai next_due mundur 1 bulan
            $next_invoice = Carbon::parse($pppoe->next_invoice);
            $periodStart = $next_invoice->copy()->subMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();
            $item = "Internet: {$pppoe->id_pelanggan} | {$pppoe->c_profile->name}";
        }

        $subscribe = $periodStart->format('d/m/Y') . ' s.d ' . $periodEnd->format('d/m/Y');

        if (!$hasPreviousInvoice) {
            // Belum ada invoice → cek apakah bukan tanggal 1 → hitung prorata
            if ($regDay->day !== 1) {
                $daysInMonth = $periodEnd->day;
                $usedDays = $periodEnd->day - $regDay->day + 1;
                $dailyPrice = round($pppoe->c_profile->price / $daysInMonth);
                $price = round($dailyPrice * $usedDays);
            } else {
                $price = $pppoe->c_profile->price;
            }
        } else {
            // Ada invoice sebelumnya → selalu ambil full price
            $price = $pppoe->c_profile->price;
        }

        $nextInvoice = Carbon::parse($pppoe->next_invoice)
            ->addMonth()
            ->toDateString();

        $dueDate = Carbon::parse($pppoe->next_invoice)->copy()->day($dueDay);

        $noInvoice = $this->generateInvoiceNumber();

        return [
            'period_start' => $periodStart,
            'subscribe'    => $subscribe,
            'price'        => $price,
            'item'         => $item,
            'next_invoice' => $nextInvoice,
            'due_date'     => $dueDate,
            'no_invoice'   => $noInvoice,
        ];
    }

    protected function generateInvoiceNumber(): string
    {
        return now()->format('m') . rand(10000000, 99999999);
    }

    protected function sendWaNotification($pppoe, $invoice, string $template, Mpwa $mpwaConfig, string $shortname)
    {
        $amountPpn = ($invoice->price * $invoice->ppn) / 100;
        $amountDiscount = $invoice->discount;
        $total = $invoice->price + $amountPpn - $amountDiscount;

        $description_adon = [];
        $mappingadons = MappingAdons::query()->where('id_pelanggan_pppoe', $pppoe->id_pelanggan)->where('no_invoice', $invoice->no_invoice)->get();
        if($mappingadons){
            foreach ($mappingadons as $mapp){
                $description_adon[] = $mapp->description;
            }
        }

        $placeholders = [
            '[nama_lengkap]', '[id_pelanggan]', '[username]', '[password]', '[alamat]', '[paket_internet]',
            '[tipe_pembayaran]', '[siklus_tagihan]', '[no_invoice]', '[tgl_invoice]',
            '[harga]', '[ppn]', '[discount]', '[total]', '[jth_tempo]', '[periode]', '[subscribe]', '[link_pembayaran]'
            ,'[description_adon]','[total_adons]','[total_invoice]'
        ];

        $values = [
            $pppoe->full_name,
            $pppoe->id_pelanggan,
            $pppoe->username,
            $pppoe->value,
            $pppoe->address,
            $pppoe->c_profile->name,
            $pppoe->payment_type,
            $pppoe->billing_period,
            $invoice->no_invoice,
            Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y'),
            number_format($invoice->price, 0, ',', '.'),
            $invoice->ppn,
            $invoice->discount,
            number_format(($total + $invoice->price_adon), 0, ',', '.'),
            Carbon::parse($invoice->due_date)->translatedFormat('d F Y'),
            Carbon::parse($invoice->period)->translatedFormat('F Y'),
            $invoice->subscribe,
            $invoice->payment_url,
            implode(', ', $description_adon),
            number_format($invoice->price_adon,0,',','.'),
            number_format(($total), 0, ',', '.')
        ];

        $message = str_replace($placeholders, $values, $template);
        $message = str_replace('<br>', "\n", $message);

        try {
            $response = Http::asForm()->post("https://{$mpwaConfig->mpwa_server}/send-message", [
                'api_key' => $mpwaConfig->api_key,
                'sender'  => $mpwaConfig->sender,
                'number'  => $pppoe->wa,
                'message' => $message,
            ]);

            if (! $response->successful()) {
                Log::error("[BillingCycle][{$shortname}] WA failed ({$response->status()}) for {$pppoe->username}: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("[BillingCycle][{$shortname}] Exception sending WA for {$pppoe->username}: " . $e->getMessage());
        }
    }
}
