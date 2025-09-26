<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MembersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Fetch the data to be exported.
     */
    public function collection()
    {
        $data = Member::where('group_id', $this->groupId)
            ->get()
            ->map(function ($item) {
                return [
                    'id_member' => $item->id_member,
                    'full_name' => $item->full_name,
                    'email' => $item->email,
                    'wa' => $item->wa,
                    'address' => $item->address,
                ];
            });

        return collect($data);
    }

    /**
     * Define column headings.
     */
    public function headings(): array
    {
        return [
            'ID Member',
            'Full Name',
            'Email',
            'WhatsApp',
            'Address',
        ];
    }
}
