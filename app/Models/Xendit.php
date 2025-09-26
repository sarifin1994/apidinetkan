<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xendit extends Model
{
    // protected $connection = 'frradius';
    protected $table = 'xendits';
    protected $fillable = [
        'group_id',
        'public_key',
        'secret_key',
        'webhook_verification_key',
        'admin_fee',
        'status',
    ];
}
