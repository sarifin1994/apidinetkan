<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Data\Services\MemberService;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class MembersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $logs = [];
        $service = new MemberService();

        unset($rows[0]);

        foreach ($rows as $index => $row) {
            $member = Member::where('group_id', Auth::user()->id_group)
                ->where('id_member', $row[0])->first();

            if ($member) {
                $logs[] = 'Skipped the row ' . $index + 1 . ' because the member with ID ' . $row[0] . ' is already exists';
                continue;
            }

            $memberId = !empty($row[0]) ? $row[0] : $service->generateMemberId(Auth::user());

            Member::create([
                'group_id' => Auth::user()->id_group,
                'id_member' => $memberId,
                'full_name' => $row[1],
                'email' => $row[2],
                'wa' => $row[3],
                'address' => $row[4],
            ]);

            $logs[] = 'Successfully added the row ' . $index + 1 . ' with member ID ' . $memberId;
        }

        blink()->put('import_logs', $logs);
    }
}
