<?php

namespace App\Exports;

use App\Models\PppoeUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PppoeUsersExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return PppoeUser::with(['rarea', 'rprofile', 'nas'])->get();
    }

    public function map($pppoeUser): array
    {
        return [
            $pppoeUser->id,
            $pppoeUser->billing,
            $pppoeUser->session_internet,
            $pppoeUser->member_name,
            $pppoeUser->username,
            $pppoeUser->profile_name ?? 'Unknown',
            $pppoeUser->nas_name ?? 'all',
            $pppoeUser->area_name ?? '-',
            $pppoeUser->odp_name ?? '-',
            $pppoeUser->created_at->format('d/m/Y H:i:s'),
            $pppoeUser->status_label, // Assuming you have an accessor for status
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'BILL',
            'INET',
            'NAMA LENGKAP',
            'USERNAME',
            'PROFILE',
            'NAS',
            'AREA',
            'ODP',
            'CREATED',
            'STATUS',
        ];
    }
}
