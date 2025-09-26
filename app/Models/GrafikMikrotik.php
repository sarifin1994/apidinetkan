<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GrafikMikrotik extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $connection = 'db_skuy';
    protected $table = 'grafik_mikrotik';
    protected $fillable = [
        'id_mikrotik',
        'vlan_id',
        'vlan_name',
        'interface',
        'rx_packets_per_second',
        'rx_bits_per_second',
        'fp_rx_packets_per_second',
        'fp_rx_bits_per_second',
        'rx_drops_per_second',
        'rx_errors_per_second',
        'tx_packets_per_second',
        'tx_bits_per_second',
        'fp_tx_packets_per_second',
        'fp_tx_bits_per_second',
        'tx_drops_per_second',
        'tx_queue_drops_per_second',
        'tx_errors_per_second',
    ];
}
