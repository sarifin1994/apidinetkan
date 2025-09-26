<?php

namespace App\Models\Invoice;

use App\Models\Pppoe\PppoeUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'invoice';
    protected $fillable = [
        'shortname',
        'id_pelanggan',
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
        'payment_url',
        'snap_token',
        'paid_date',
        'status',
        'mitra_id',
        'komisi',
        'price_adon_monthly',
        'price_adon'
    ];

    public function rpppoe(){
        return $this->belongsTo(PppoeUser::class, 'id_pelanggan','id')->withDefault(); 
    }

    public function c_pppoe(){
        return $this->hasOne(PppoeUser::class, 'id','id_pelanggan'); 
    }

}
