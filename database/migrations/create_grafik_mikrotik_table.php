<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grafik_mikrotik', function (Blueprint $table) {
            $table->id();
            $table->string('vlan_id')->default('');
            $table->string('vlan_name')->default('');
            $table->string('interface')->default('');
            $table->integer('rx_packets_per_second')->default(0);
            $table->integer('rx_bits_per_second')->default(0);
            $table->integer('fp_rx_packets_per_second')->default(0);
            $table->integer('fp_rx_bits_per_second')->default(0);
            $table->integer('rx_drops_per_second')->default(0);
            $table->integer('rx_errors_per_second')->default(0);
            $table->integer('tx_packets_per_second')->default(0);
            $table->integer('tx_bits_per_second')->default(0);
            $table->integer('fp_tx_packets_per_second')->default(0);
            $table->integer('fp_tx_bits_per_second')->default(0);
            $table->integer('tx_drops_per_second')->default(0);
            $table->integer('tx_queue_drops_per_second')->default(0);
            $table->integer('tx_errors_per_second')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grafik_mikrotik');
    }
};
