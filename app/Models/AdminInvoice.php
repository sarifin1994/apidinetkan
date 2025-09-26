<?php

namespace App\Models;

use App\Enums\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminInvoice extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'admin_invoices';
    protected $fillable = [
        'group_id',
        'itemable_id',
        'itemable_type',
        'no_invoice',
        'item',
        'price',
        'ppn',
        'fee',
        'discount',
        'discount_coupon',
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
        'coupon_name'
    ];

    protected $casts = [
        'status' => InvoiceStatusEnum::class,
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'group_id', 'id_group');
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
