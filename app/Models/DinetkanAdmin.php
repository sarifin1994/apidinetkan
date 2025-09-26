<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DinetkanAdmin extends Authenticatable
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'dinetkan_admin';
    protected $fillable = [
        'shortname',
        'name',
        'username',
        'password',
        'status',
        'role',
    ];
}
