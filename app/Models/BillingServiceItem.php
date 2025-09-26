<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingServiceItem extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'billing_service_item';
    protected $fillable = [
        'dinetkan_user_id',
        'id_member',
        'total_price',
        'total_ppn',
        'total_bhp',
        'total_uso',
        'month',
        'year',
    ];

    public function member_dinetkan(){
        return $this->hasOne(MemberDinetkan::class, 'id_member','id_member');
    }
}
