<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    //
    protected $fillable = [
        'shortname', 'host', 'port', 'encryption', 'username', 'password', 'sender_name'
    ];
    // Enkripsi otomatis saat set/get password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        return Crypt::decryptString($value);
    }
}
