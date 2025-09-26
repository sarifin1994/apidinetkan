<?php

namespace App\Models;

use App\Enums\DinetkanInvoiceStatusEnum;
use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDinetkanInvoice extends Model
{
    use HasFactory;
    use Userstamps;
    // protected $connection = 'frradius';
    protected $table = 'admin_dinetkan_invoices';
    protected $fillable = [
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
        'coupon_name',
        'price_otc',
        'ppn_otc',
        'dinetkan_user_id',
        'notes',
        'pay_from',
        'is_otc',
        'total_ppn',
        'price_adon',
        'price_adon_monthly',
        'virtual_account',
        'bank',
        'bank_name',
        'id_mapping',
        'create_at_by','update_at_by','is_upgrade'
    ];

    protected $casts = [
        'status' => DinetkanInvoiceStatusEnum::class,
    ];

    public function admin()
    {
        return $this->belongsTo(UserDinetkan::class, 'dinetkan_user_id', 'dinetkan_user_id');
    }

    public function mapping()
    {
        return $this->belongsTo(AdminDinetkanInvoice::class, 'id_mapping', 'id');
    }

    public function itemable()
    {
        return $this->morphTo();
    }

    public function mapping_mitra()
    {
        return $this->belongsTo(MappingUserLicense::class, 'id_mapping', 'id')->where('id_mitra', multi_auth()->id_mitra);
    }

//    public function mapping_mitra_admin()
//    {
//        return $this->belongsTo(MappingUserLicense::class, 'id_mapping', 'id');
//    }

    public function mapping_mitra_admin()
    {
        return $this->belongsTo(MappingUserLicense::class, 'id_mapping', 'id')
            ->withDefault([
                'id'   => 0,
                'nama' => 'Belum ada mapping',
            ]);
    }

}
