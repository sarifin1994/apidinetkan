<?php

namespace App\Models;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenisGangguan extends Model
{
    use HasFactory;
//    use Userstamps;
    // protected $connection = 'db_skuy';
    protected $table = 'master_jenis_gangguan';
    protected $fillable = [
        'name',
        'send_group',
        'group_tiket'
    ];

    protected $casts = [
        'group_tiket' => 'array',
    ];
}
