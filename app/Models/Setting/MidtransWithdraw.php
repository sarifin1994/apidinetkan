<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class MidtransWithdraw extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'withdraw_midtrans';
    protected $fillable = [
        'shortname',
        'tanggal',
        'id_penarikan',
        'nominal',
        'nomor_rekening',
        'atas_nama',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'shortname','username')->withDefault(); 
    }
}
