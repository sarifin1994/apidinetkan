<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Models\Pppoe\PppoeUser;

class PppoeUserExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return PppoeUser::query()->where('shortname',multi_auth()->shortname)->select('shortname', 'username', 'value','profile','nas','id_pelanggan','full_name','address','wa','payment_type','billing_period','ppn','discount','reg_date','next_due','status'); // Sesuaikan dengan kolom yang ingin diekspor
    }

    // Menentukan heading (judul kolom)
    public function headings(): array
    {
        return ['Shortname', 'Username', 'Password', 'Profile', 'NAS', 'ID Pelanggan', 'Nama Lengkap', 'Alamat', 'Nomor WA', 'Payment Type', 'Billing Periode', 'PPN', 'Discount', 'Reg Date', 'Next Due', 'Status'];
    }

    // Menentukan data yang akan diekspor (mapping)
    public function map($row): array
    {
        return [$row->shortname, $row->username, $row->value, $row->profile,$row->nas,$row->id_pelanggan,$row->full_name,$row->address,$row->wa,$row->payment_type,$row->billing_period,$row->ppn,$row->discount,$row->reg_date,$row->next_due,$row->status];
    }
}
