<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_message_id','file'
    ];

    // field virtual
    protected $appends = ['url_file'];

    public function getUrlFileAttribute()
    {
        $radiusUrl = config('services.radius.url');
        return $radiusUrl.'/api/tickets/file/'.$this->id;
    }
}

