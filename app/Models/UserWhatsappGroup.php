<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWhatsappGroup extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'user_whatsapp_group';
    protected $fillable = [
        'group_id',
        'group_name',
        'user_id'
    ];
}
