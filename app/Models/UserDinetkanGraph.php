<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDinetkanGraph extends Model
{
    use HasFactory;
    // protected $connection = 'db_skuy';
    protected $table = 'user_dinetkan_graph';
    protected $fillable = [
        'dinetkan_user_id',
        'graph_name',
        'graph_id',
        'pop_id',
        'service_id'
    ];
}
