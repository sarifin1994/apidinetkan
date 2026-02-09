<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number','dinetkan_user_id','name','email','subject',
        'department','priority','service_id','status','closed_date'
    ];

    public function messages(){
        return $this->hasMany(TicketMessage::class);
    }
}

