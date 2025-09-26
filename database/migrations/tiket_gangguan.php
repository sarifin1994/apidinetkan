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
        Schema::connection('mysql')->create('tiket_gangguan', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('nomor_tiket');
            $table->string('pelanggan_id');
            $table->string('nama_pelanggan');
            $table->string('jenis_gangguan');
            $table->string('prioritas')->default('normal','rendah','tinggi');
            $table->string('note')->nullable();
            $table->string('penyelesaian')->nullable();
            $table->string('teknisi');
            $table->string('created_by');
            $table->string('status')->default('open');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_gangguan');
    }
};
