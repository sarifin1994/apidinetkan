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
        Schema::connection('mysql')->create('partnership_mitra', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('name');
            $table->string('id_mitra')->unique();
            $table->string('password');
            $table->integer('login'); // 1 ya // 0 tidak
            $table->integer('user'); // 1 ya // 0 tidak
            $table->integer('billing'); // 1 ya // 0 tidak
            $table->string('nomor_wa');
            $table->string('profile')->nullable();
            $table->integer('status')->default(1); // 1 aktif // 0 nonaktif
            $table->string('role')->default('Mitra');


            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_mitra');
    }
};
