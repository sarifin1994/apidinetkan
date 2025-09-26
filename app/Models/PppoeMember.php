<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PppoeMember extends Model
{
    // protected $connection = 'frradius';
    protected $table = 'frradius.pppoe_member';

    protected $fillable = [
        'group_id',
        'id_service',
        'pppoe_id',
        'member_id',
        'profile_id',
        'kode_area',
        'payment_type',
        'billing_period',
        'ppn',
        'discount',
        'reg_date',
        'next_due',
        'next_invoice',
        'tgl',
    ];

    public function data()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function pppoe()
    {
        return $this->belongsTo(PppoeUser::class, 'pppoe_id', 'id');
    }

    public function profile()
    {
        return $this->belongsTo(PppoeProfile::class, 'profile_id', 'id');
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'member_id', 'member_id')->orderBy('id', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'pppoe_member_id', 'id')->orderBy('id', 'desc');
    }
}
