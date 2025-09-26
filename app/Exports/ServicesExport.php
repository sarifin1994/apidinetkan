<?php

namespace App\Exports;

use App\Models\PppoeUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServicesExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $data = PppoeUser::with([
            'member:id_service,pppoe_id,member_id,payment_type,billing_period,reg_date,next_due',
            'member.data:id,id_member,full_name',
        ])
            ->where('group_id', $this->groupId)
            ->get()
            ->map(function ($item) {
                return [
                    'id_service'   => $item->member->id_service ?? '',
                    'id_member'    => $item->member->data->id_member ?? '',
                    'name'         => $item->member->data->full_name ?? '',
                    'username'     => $item->username,
                    'password'     => $item->value,
                    'profile'      => $item->profile,
                    'nas'          => $item->nas,
                    'ip_address'   => $item->ip_address,
                    'type'         => $item->type,
                    'lock_mac'     => $item->mac,
                    'area'         => $item->kode_area,
                    'odp'          => $item->kode_odp,
                    'payment_type' => $item->member->payment_type ?? '',
                    'billing'      => $item->member->billing_period ?? '',
                    'ppn'          => $item->member->ppn ?? '',
                    'discount'     => $item->member->discount ?? '',
                    'active_date'  => $item->member->reg_date ?? '',
                    'due_date'     => $item->member->next_due ?? '',
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
            'id_service',
            'id_member',
            'name',
            'username',
            'password',
            'profile',
            'nas',
            'ip_address',
            'type',
            'lock_mac',
            'area',
            'odp',
            'payment_type',
            'billing',
            'ppn',
            'discount',
            'active_date',
            'due_date',
        ];
    }
}
