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
        Schema::connection('frradius_auth')->create('nas', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('nasname')->unique();
            $table->string('type')->default('other');
            $table->string('secret');
            $table->string('timezone');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nas');
    }
};
