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
        Schema::connection('mysql')->create('mpwa', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('mpwa_server');
            $table->string('sender')->unique();
            $table->integer('user_id')->unique();
            $table->string('api_key')->unique();
            $table->string('webhook')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpwa');
    }
};
