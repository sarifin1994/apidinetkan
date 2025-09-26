<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\HotspotUser;
use Carbon\Carbon;

class HotspotImport implements ToModel
{
    /**
    * @param array $row
    */
    public function model(array $row)
    {
        return new HotspotUser([
            'group_id' => auth()->user()->id_group,
            'shortname' => auth()->user()->shortname,
            'username' => $row[1], 
            'value' => $row[2], 
            'profile' => $row[3], 
            // 'nas' => $row[4], 
            // 'server' => $row[5], 
            'status' => 1, 
            'admin' => auth()->user()->username,
            // 1 unpaid
            'statusPayment' => 1,
            'created_at' => Carbon::now(),
        ]);
    }
}
