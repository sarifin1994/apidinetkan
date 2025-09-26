<?php

namespace App\Models;

use App\Enums\TransactionPaymentMethodEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Member;

class Transaksi extends Model
{
    use HasFactory;
    // protected $connection = 'frradius';
    protected $table = 'transaksi';
    protected $fillable = [
        'group_id',
        'invoice_id',
        'invoice_type',
        'type',
        'category',
        'item',
        'deskripsi',
        'price',
        'tanggal',
        'payment_method',
        'admin',
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class,
        'category' => TransactionCategoryEnum::class,
        'payment_method' => TransactionPaymentMethodEnum::class,
    ];

    public function invoice()
    {
        return $this->morphTo();
    }
}
