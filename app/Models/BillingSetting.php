<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'setting';
    protected $fillable = [
        'group_id',
        'due_bc',
        'inv_fd',
        'suspend_date',
        'suspend_time',
        'notif_bi',
        'notif_it',
        'notif_ps',
        'notif_sm',
        'payment_gateway',
        'bank_account',
        // 'merge_inv',
        // 'footer_wa',
        // 'signature_wa',
    ];
}
