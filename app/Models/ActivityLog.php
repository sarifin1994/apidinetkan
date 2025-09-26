<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Partnership\Mitra;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

class ActivityLog extends Model
{
    use HasFactory;
    // use LogsActivity;
    protected $connection = 'mysql';
    protected $table = 'activity_log';
    protected $fillable = ['shortname'];

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'causer_id')->withDefault();
    // }
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
