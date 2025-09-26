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
        Schema::connection('mysql')->create('vpn_server', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->string('name');
            $table->string('host')->unique();
            $table->string('user');
            $table->string('password');
            $table->integer('port');
            $table->integer('status');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpn_server');
    }
};
