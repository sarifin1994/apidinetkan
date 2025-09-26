<?php

namespace App\Models;

use App\Enums\NewClientTicketStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PppoeUser;
use App\Models\Member;

class TicketPsb extends Model
{
    use HasFactory;
    protected $connection = 'db_ftth';
    protected $table = 'ticket_psb';
    protected $fillable = [
        'group_id',
        'id_psb',
        'nama_lengkap',
        'no_wa',
        'alamat',
        'paket',
        'paket_id',
        'pppoe_id',
        'member_id',
        'status',
        'tgl_psb',
        'tgl_aktif',
        'note',
        'file_ktp',
        'closed_by'
    ];

    protected $casts = [
        'status' => NewClientTicketStatusEnum::class,
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
