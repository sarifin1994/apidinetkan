<?php

namespace App\Imports;

use App\Models\Hotspot\HotspotUser;
use App\Models\Hotspot\HotspotProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Owner\License;
use Illuminate\Support\Facades\Log;
use App\Models\Setting\BillingSetting;
use App\Models\Partnership\Reseller;
use App\Models\Mikrotik\Nas;

class HotspotUserImport implements ToCollection, WithStartRow, WithMultipleSheets
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
            $created_at = now();
            foreach ($rows as $row) {
                if ($row->filter()->isEmpty()) {
                    continue; // Lewati baris kosong
                }
                // Cek limit license untuk Hotspot User
                $currentCount = HotspotUser::where('shortname', multi_auth()->shortname)->count();
                $licenseLimit = License::where('id', multi_auth()->license_id)->select('limit_hs')->first()->limit_hs;

                if ($currentCount >= $licenseLimit) {
                    throw new \Exception('Maaf lisensi anda sudah limit, silakan upgrade!');
                }

                if ($row[6] === 'UNPAID') {
                    $statusPayment = 1;
                } else {
                    $statusPayment = 2;
                }

                if ($row[4] === null) {
                    $nas = null;
                } else {
                    $nas = Nas::where('shortname',multi_auth()->shortname)->where('name', $row[4])->select('ip_router')->first()->ip_router;
                }
                if ($row[5] === null) {
                    $reseller = null;
                } else {
                    $reseller = Reseller::where('shortname',multi_auth()->shortname)->where('name', $row[5])->select('id')->first()->id;
                }
                // Buat data PPPoE User
                HotspotUser::create([
                    'shortname' => multi_auth()->shortname,
                    'username' => $row[1],
                    'value' => $row[2],
                    'profile' => $row[3],
                    'nas' => $nas,
                    'reseller_id' => $reseller,
                    'statusPayment' => $statusPayment,
                    'status' => 1,
                    'created_by' => multi_auth()->username,
                    'created_at' => $created_at,
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
