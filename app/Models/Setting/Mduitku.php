<?php

namespace App\Models\Setting;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mduitku extends Model
{
    use HasFactory;
    use Userstamps;
    protected $connection = 'mysql';
    protected $table = 'setting_duitku';
    protected $fillable = [
        'shortname',
        'id_merchant',
        'api_key',
        'callback_url',
        'return_url',
        'admin_fee',
        'status',
        'user_id',
        'secret_key',
        'status_widrawal',
        'fee_disburs',
        'email_disburs',
        'minimal_disburs',
        'create_at_by','update_at_by'

    ];
}
