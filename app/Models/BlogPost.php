<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'category_id','title','slug','excerpt',
        'content','thumbnail','status','hashtags','thumbnail_url'
    ];

    public function category(){
        return $this->belongsTo(BlogCategory::class);
    }
}

