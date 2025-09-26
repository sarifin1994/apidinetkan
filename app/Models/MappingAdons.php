<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MappingAdons extends Model
{
    use HasFactory, Notifiable;
    protected $connection = 'mysql';
    protected $table = 'mapping_adons';
    protected $fillable = [
        'id_mapping',
        'description',
        'ppn',
        'monthly',
        'qty',
        'price',
        'no_invoice',
        'id_pelanggan_pppoe'
    ];
}
