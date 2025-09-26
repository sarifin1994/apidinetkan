<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'setting_company';
    protected $fillable = [
        'shortname',
        'name',
        'singkatan',
        'slogan',
        'bank',
        'holder',
        'email',
        'wa',
        'address',
        'website',
        'logo',
        'note',
    ];
}
