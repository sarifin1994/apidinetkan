<?php

namespace App\Imports;

use App\Models\Pppoe\PppoeUser;
use App\Models\Pppoe\PppoeProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Owner\License;
use Illuminate\Support\Facades\Log;
use App\Models\Setting\BillingSetting;
use App\Models\Mikrotik\Nas;

class PppoeUserImport implements ToCollection, WithStartRow, WithMultipleSheets
{
    /**
     * Mulai membaca dari baris kedua (melewati header).
     */
    public function startRow(): int
    {
        return 6;
    }

    public function sheets(): array
    {
        return [
            0 => $this, // Hanya sheet pertama yang diproses
        ];
    }
    public function collection(Collection $rows)
    {
        \DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if ($row->filter()->isEmpty()) {
                    continue; // Lewati baris kosong
                }
                // Ambil data dari baris (tanpa skip kolom pertama)
                $payment_type = $row[11]; // Payment type
                $billing_period = $row[12]; // Billing period
                $reg_date = $row[15]; // Tanggal aktif

                // Cek limit license untuk PPPoE User
                $currentCount = PppoeUser::where('shortname', multi_auth()->shortname)->count();
                $licenseLimit = License::where('id', multi_auth()->license_id)->select('limit_pppoe')->first()->limit_pppoe;

                if ($currentCount >= $licenseLimit) {
                    throw new \Exception('Maaf lisensi anda sudah limit, silakan upgrade!');
                }

                // Perhitungan tanggal aktif berdasarkan payment_type dan billing_period
                if ($payment_type === 'Prabayar' && $billing_period === 'Fixed Date') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $tgl = date('d', strtotime($reg_date));
                } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Fixed Date') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $tgl = date('d', strtotime($reg_date));
                } elseif ($payment_type === 'Pascabayar' && $billing_period === 'Billing Cycle') {
                    $due_bc = BillingSetting::where('shortname', multi_auth()->shortname)->select('due_bc')->first();
                    $next_due = Carbon::createFromFormat('Y-m-d', $reg_date)->setDay($due_bc->due_bc)->addMonths(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $reg_date)->startOfMonth()->addMonths(1);
                    $tgl = date('d', strtotime($reg_date));
                }else if ($payment_type === 'Prabayar' && $billing_period === 'Renewable') {
                    $next_due = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $next_invoice = Carbon::createFromFormat('Y-m-d', $reg_date)->addMonthsWithNoOverflow(1);
                    $tgl = date('d', strtotime($reg_date));
                } else{
                    $next_due = NULL;
                    $next_invoice = NULL;
                    $tgl = NULL;
                }
                if ($row[4] === null || $row[4] === '') {
                    $nas = null;
                } else {
                    $nas = Nas::where('shortname',multi_auth()->shortname)->where('name', $row[4])->select('ip_router')->first()->ip_router;
                }
                // Buat data PPPoE User
                PppoeUser::create([
                    'shortname' => multi_auth()->shortname,
                    'username' => $row[1],
                    'value' => $row[2],
                    'profile' => $row[3],
                    'nas' => $nas,
                    'status' => 1,
                    'lock_mac' => 0,
                    'id_pelanggan' => $row[5],
                    'profile_id' => PppoeProfile::where('name', $row[3])
                    ->where('shortname', multi_auth()->shortname)
                    ->select('id')
                    ->first()
                    ->id,
                    'full_name' => $row[6],
                    'wa' => $row[7],
                    'address' => $row[8],
                    'kode_area' => $row[9],
                    'kode_odp' => $row[10],
                    'payment_type' => $payment_type,
                    'billing_period' => $billing_period,
                    'ppn' => $row[13],
                    'discount' => $row[14],
                    'reg_date' => $reg_date,
                    'next_due' => $next_due,
                    'next_invoice' => $next_invoice,
                    'tgl' => $tgl,
                    'created_by' => multi_auth()->username,
                    'created_at' => Carbon::now()->addMinute(),
                ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error processing row during import: ' . json_encode($rows->toArray()) . ' Error: ' . $e->getMessage());
            throw $e; // Agar error ditangani oleh controller atau middleware
        }
        return null;
    }
}
