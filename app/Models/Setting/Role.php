<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'setting_role';
    protected $fillable = [
        'shortname',
        'teknisi_status_regist',
        'kasir_melihat_total_keuangan',
    ];
}
