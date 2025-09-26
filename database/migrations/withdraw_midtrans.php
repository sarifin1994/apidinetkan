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
        Schema::connection('mysql')->create('withdraw_midtrans', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->datetime('tanggal');
            $table->string('id_penarikan')->unique();
            $table->string('nominal');
            $table->string('nomor_rekening');
            $table->string('status')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_midtrans');
    }
};
