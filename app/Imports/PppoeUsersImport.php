<?php

namespace App\Imports;

use App\Models\PppoeUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PppoeUsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new PppoeUser([
            'username' => $row['username'],
            'password' => bcrypt($row['password']), // Ensure passwords are hashed
            'profile_id' => $row['profile_id'],
            'nas' => $row['nas'],
            'rarea_id' => $row['rarea_id'],
            'kode_odp' => $row['kode_odp'],
            'lock_mac' => $row['lock_mac'],
            'mac' => $row['mac'],
            'full_name' => $row['full_name'],
            'wa' => $row['wa'],
            'address' => $row['address'],
            'payment_type' => $row['payment_type'],
            'payment_status' => $row['payment_status'],
            'billing_period' => $row['billing_period'],
            'reg_date' => \Carbon\Carbon::parse($row['reg_date']),
            'ppn' => $row['ppn'],
            'discount' => $row['discount'],
            'amount' => $row['amount'],
            'payment_total' => $row['payment_total'],
            'status' => $row['status'],
        ]);
    }
}
