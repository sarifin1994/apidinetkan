<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Partnership\Mitra;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

class BankAccount extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'bank_account';
    protected $fillable = [
        'id_mitra',
        'bank_name',
        'bank_code',
        'account_name',
        'account_number'
    ];
}
