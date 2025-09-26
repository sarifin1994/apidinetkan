<?php

namespace App\Models;

use App\Models\Traits\Userstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPop extends Model
{
    use HasFactory;
    use Userstamps;
    // protected $connection = 'db_skuy';
    protected $table = 'master_pop';
    protected $fillable = [
        'name',
        'pic_name',
        'pic_whatsapp',
        'ip',
        'create_at_by','update_at_by'

    ];
}
