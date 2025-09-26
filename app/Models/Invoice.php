<?php

namespace App\Models;

use App\Enums\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Member;

class Invoice extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'invoice';
    protected $fillable = [
        'group_id',
        'pppoe_id',
        'member_id',
        'pppoe_member_id',
        'no_invoice',
        'item',
        'price',
        'ppn',
        'discount',
        'invoice_date',
        'due_date',
        'period',
        'subscribe',
        'payment_type',
        'billing_period',
        'paid_date',
        'status',
        'payment_url',
        'snap_token',
    ];

//    protected $casts = [
//        'status' => InvoiceStatusEnum::class,
//    ];

    public function pppoe()
    {
        return $this->belongsTo(PppoeUser::class, 'pppoe_id', 'id')->withDefault();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id')->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(PppoeMember::class, 'pppoe_member_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'group_id', 'group_id')->withDefault();
    }

    public function billingSetting()
    {
        return $this->belongsTo(BillingSetting::class, 'group_id', 'group_id')->withDefault();
    }

    public function group()
    {
        return $this->belongsTo(User::class, 'group_id', 'id_group')->withDefault();
    }
}
