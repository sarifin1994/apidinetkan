<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingSetting extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'setting_billing';
    protected $fillable = [
        'shortname',
        'due_bc',
        'inv_fd',
        'suspend_date',
        'notif_ir',
        'notif_it',
        'notif_ps',
        'notif_sm',
    ];
}
