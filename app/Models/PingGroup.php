<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PingGroup extends Model
{
    protected $fillable = ['name'];

    public function targets()
    {
        return $this->hasMany(PingTarget::class,'group_id');
    }
}
