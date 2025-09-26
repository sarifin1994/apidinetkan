<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLicenseDinetkan extends Model
{
    use HasFactory;
//    protected $connection = 'db_skuy';
    protected $table = 'category_license_dinetkan';
    protected $fillable = [
        'id',
        'name'
    ];
}
