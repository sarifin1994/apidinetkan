<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balancehistory extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'balance_history';
    protected $fillable = [
        'id_mitra',
        'id_transaksi',
        'id_reseller',
        'tx_amount',
        'notes',
        'type',
        'tx_date',
        'is_widraw'
    ];
}
