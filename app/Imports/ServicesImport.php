<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\PppoeMember;
use App\Models\PppoeProfile;
use App\Models\PppoeUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Modules\Data\Services\PppoeService;

HeadingRowFormatter::default('none');
class ServicesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $service = new PppoeService();
        $group_id = Auth::user()->id_group;
        $logs = [];

        $firstRow = $rows->first();

        if ($firstRow[0] !== 'id_service') {
            $logs[] = 'Skipped the import process because the first column is not id_service';
            blink()->put('import_logs', $logs);
            return;
        }

        unset($rows[0]);

        foreach ($rows as $index => $row) {
            $member = Member::where('group_id', $group_id)
                ->where('id_member', $row[1])->first();
            $pppoeMember = PppoeMember::where('group_id', $group_id)
                ->where('id_service', $row[0])->first();
            $service = $pppoeMember ? $pppoeMember->pppoe->id : null;

            if (!$member && $row[1]) {
                $logs[] = 'Skipped the row ' . ($index + 1) . ' because the member with ID ' . $row[1] . ' is not found';
                continue;
            }

            if ($service) {
                $logs[] = 'Skipped the row ' . ($index + 1) . ' because the service with ID ' . $row[0] . ' is already exists';
                continue;
            }

            $profile = PppoeProfile::where('name', $row[4])->first();

            if (!$profile) {
                $logs[] = 'Skipped the row ' . ($index + 1) . ' because the profile with name ' . $row[4] . ' is not found';
                continue;
            }

            $pppoeUser = PppoeUser::create([
                'group_id' => $group_id,
                'shortname' => $member->full_name,
                'username' => $row[2],
                'value' => $row[3],
                'profile' => $profile->name,
                'nas' => $row[5],
                'status' => 1,
                'type' => $row[7],
                'lock_mac' => empty($row[7]) ? false : true,
                'mac' => $row[8],
                'member_name' => $member->full_name,
                'kode_area' => $row[9],
                'kode_odp' => $row[10],
            ]);

            $activeDate = trim($row[15]);
            $activeDate = strpos($activeDate, ' ') !== false ? explode(' ', $activeDate)[0] : $activeDate;
            $dueDate = trim($row[16]);
            $dueDate = strpos($dueDate, ' ') !== false ? explode(' ', $dueDate)[0] : $dueDate;
            $dueDate = '0000-00-00' === $dueDate ? null : $dueDate;

            // Convert d/m/Y format to Y-m-d, but if the date is Y-m-d, then no need to convert
            $regDate = strpos($activeDate, '/') !== false ? Carbon::createFromFormat('d/m/Y', $activeDate)->format('Y-m-d') : $activeDate;
            $nextDue = strpos($dueDate, '/') !== false ? Carbon::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d') : $dueDate;

            // get day from reg_date
            $tgl = Carbon::parse($regDate)->format('d');

            $pppoeMember = PppoeMember::create([
                'group_id' => $group_id,
                'id_service' => null,
                'pppoe_id' => $pppoeUser->id,
                'member_id' => $member->id,
                'profile_id' => $profile->id,
                'kode_area' => $row[9],
                'payment_type' => $row[11],
                'billing_period' => $row[12],
                'ppn' => $row[13],
                'discount' => $row[14],
                'reg_date' => $regDate,
                'next_due' => $nextDue,
                'next_invoice' => $nextDue,
                'tgl' => $tgl,
            ]);

            $serviceId = !empty($row[0]) ? $row[0] : $service->generateServiceId($pppoeUser, $pppoeMember);
            $pppoeMember->update(['id_service' => $serviceId]);

            $logs[] = 'Successfully added the row ' . ($index + 1) . ' with service ID ' . $serviceId;
        }

        blink()->put('import_logs', $logs);
    }
}
