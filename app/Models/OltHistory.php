<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltHistory extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'onu_history';
    protected $fillable = [
        'group_id',
        'id_olt',
        'id_onu',
        'history_desc',
        'desc_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'group_id','id_group');
    }

}
