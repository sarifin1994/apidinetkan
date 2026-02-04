<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingService extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'billing_service';
    protected $fillable = [
        'dinetkan_user_id',
        'total_price',
        'total_ppn',
        'total_bhp',
        'total_uso',
        'total_member',
        'status',
        'notes',
        'paid_via',
        'month',
        'year',
        'paid_date',
        'virtual_account',
        'bank',
        'reference',
        'bank_name',
        'qrString'
    ];
}
