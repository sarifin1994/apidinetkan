<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WablasTemplate extends Model
{
    use HasFactory;
    protected $connection = 'db_wablas';
    protected $table = 'template';
    protected $fillable = [
        'group_id',
        'invoice_terbit',
        'invoice_reminder',
        'invoice_overdue',
        'payment_paid',
        'payment_cancel',
        'account_active',
        'account_suspend',

    ];
}
