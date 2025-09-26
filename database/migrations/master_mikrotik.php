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
        Schema::create('master_mikrotik', function (Blueprint $table) {
            $table->id();
            $table->integer('ip')->default(0);
            $table->integer('port')->default(0);
            $table->integer('username')->default(0);
            $table->integer('password')->default(0);
            $table->integer('time_out')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_mikrotik');
    }
};
