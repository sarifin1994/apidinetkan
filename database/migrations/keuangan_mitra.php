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
        Schema::connection('mysql')->create('keuangan_mitra', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->integer('mitra_id')->default(0);
            $table->string('id_data');
            $table->string('nas')->nullable();
            $table->datetime('tanggal');
            $table->string('tipe'); // pemasukan // pengeluaran
            $table->string('kategori');
            $table->string('deskripsi');
            $table->string('nominal');
            $table->string('metode'); 
            $table->string('created_by'); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_mitra');
    }
};
