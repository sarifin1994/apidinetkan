<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DuitkuLog extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $connection = 'db_skuy';
    protected $table = 'duiktu_log';
    protected $fillable = [
        'shortname',
        'apiKey',
        'merchantCode',
        'amount',
        'merchantOrderId',
        'productDetail',
        'additionalParam',
        'paymentMethod',
        'resultCode',
        'merchantUserId',
        'reference',
        'signature',
        'publisherOrderId',
        'spUserHash',
        'settlementDate',
        'issuerCode',
        'vaNumber',
        'notes'
    ];
}
