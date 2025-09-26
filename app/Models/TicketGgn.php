<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;
use App\Models\Member;

class TicketGgn extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'db_ftth.ticket_ggn';
    protected $fillable = [
        'group_id',
        'id_ggn',
        'nama_lengkap',
        'note',
        'tipe',
        'member_id',
        'pppoe_id',
        'kode_area',
        'jenis',
        'penyelesaian',
        'status',
        'tgl_open',
        'tgl_closed',
        'closed_by'
    ];
    public function rpppoe()
    {
        return $this->belongsTo(PppoeUser::class, 'pppoe_id')->withDefault();
    }
    public function rmember()
    {
        return $this->belongsTo(Member::class, 'member_id')->withDefault();
    }
}
