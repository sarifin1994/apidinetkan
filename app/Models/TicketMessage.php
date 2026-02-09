<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id','dinetkan_user_id','message'
    ];

    public function attachments(){
        return $this->hasMany(TicketAttachment::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'dinetkan_user_id','dinetkan_user_id');
    }
}
