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
        Schema::connection('mysql')->create('olt_device', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('type');
            $table->string('version')->nullable();
            $table->string('name');
            $table->string('host');
            $table->string('username');
            $table->string('password');
            $table->string('cookies');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_device');
    }
};
