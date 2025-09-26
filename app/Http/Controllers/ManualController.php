<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balancehistory;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\Partnership\Mitra;
use App\Settings\SiteDinetkanSettings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice\Invoice;
use App\Models\Pppoe\PppoeUser;
use App\Models\Setting\BillingSetting;
use App\Models\Whatsapp\Mpwa;
use App\Models\Whatsapp\Watemplate;
use App\Models\Radius\RadiusNas;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ManualController extends Controller
{
    public function __construct(
        private SiteDinetkanSettings $settings
    ) {
    }

    public function test_send($no_invoice, $_template){
        send_faktur_inv($no_invoice,$this->settings,$_template);
    }
    public function manual()
    {
        $today = Carbon::today()->toDateString();

        $users = User::where('role', 'Admin')
                     ->whereIn('status', [1, 3])
                     ->get();

        foreach ($users as $user) {
            Log::info("[SyncInvoices][{$user->shortname}] Start processing invoices for {$today}");

            Invoice::where('shortname', $user->shortname)
                ->where('invoice_date', $today)
                ->chunkById(100, function ($invoices) use ($user) {
                    foreach ($invoices as $invoice) {
                        $oldNumber = $invoice->no_invoice;
                        $newNumber = Str::afterLast($invoice->payment_url, '/');

                        if ($oldNumber !== $newNumber) {
                            $invoice->no_invoice = $newNumber;
                            $invoice->save();

                            // File log
                            Log::info(
                                "[SyncInvoices][{$user->shortname}] ".
                                "Invoice ID {$invoice->id}: ".
                                "no_invoice changed from {$oldNumber} to {$newNumber}"
                            );
                        }
                    }
                });

            Log::info("[SyncInvoices][{$user->shortname}] Done.");
        }

    }

    public function update_balance(){

        $keuangans = TransaksiMitra::query()->get();

        foreach ($keuangans as $transaksi){
            $cek = Balancehistory::query()->where('id_mitra', $transaksi->mitra_id)
                                            ->where('tx_amount', $transaksi->nominal)
                                            ->where('notes', $transaksi->deskripsi)
                                            ->where('id_transaksi', $transaksi->id)->first();
            if(!$cek){
                $balancehistory = Balancehistory::create([
                    'id_mitra' => $transaksi->mitra_id,
                    'id_reseller' => '',
                    'tx_amount' => $transaksi->nominal,
                    'notes' => $transaksi->deskripsi,
                    'type' => 'in',
                    'tx_date' => $transaksi->create_at,
                    'id_transaksi' => $transaksi->id
                ]);

                $updatemitra = Mitra::query()->where('id', $transaksi->mitra_id)->first();
                if($updatemitra){
                    $lastbalance = $updatemitra->balance;
                    $updatemitra->update([
                        'balance' => $lastbalance + (int)$transaksi->nominal
                    ]);
                }
            }
        }
        Log::info("[SYNC BALANCE] Done.");
    }
}
