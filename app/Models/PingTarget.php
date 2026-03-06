<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PingTarget extends Model
{
    protected $fillable = [
        'name',
        'host',
        'group_id'
    ];

    public function group()
    {
        return $this->belongsTo(PingGroup::class,'group_id');
    }
}
