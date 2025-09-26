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
        Schema::connection('mysql')->create('radius_vpn', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('vpn_server');
            $table->string('name');
            $table->string('user')->unique();
            $table->string('password');
            $table->string('ip_address')->unique();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radius_vpn');
    }
};
