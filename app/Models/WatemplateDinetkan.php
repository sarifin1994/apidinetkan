<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WatemplateDinetkan extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'whatsapp_template_dinetkan';
    protected $fillable = [
        'shortname',
        'dinetkan_user_id',
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
