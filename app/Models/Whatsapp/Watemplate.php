<?php

namespace App\Models\Whatsapp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Watemplate extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'whatsapp_template';
    protected $fillable = [
        'shortname',
        'invoice_terbit',
        'invoice_reminder',
        'invoice_overdue',
        'payment_paid',
        'payment_cancel',
        'account_active',
        'account_suspend',
        'tiket_open_pelanggan',
        'tiket_open_teknisi',
        'tiket_close_pelanggan',
        'tiket_close_teknisi',
    ];
}
