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
        Schema::connection('mysql')->create('license', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('deskripsi')->nullable();
            $table->string('spek')->nullable();
            $table->integer('price')->default(0);
            $table->integer('limit_pppoe');
            // $table->integer('limit_pppoe_online');
            $table->integer('limit_hs');
            // $table->integer('limit_hs_online');
            $table->integer('midtrans');
            $table->integer('olt');
            $table->integer('status')->default(1); //1 aktif //0 nonaktif
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license');
    }
};
