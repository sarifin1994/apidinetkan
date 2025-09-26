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
        Schema::connection('frradius_auth')->create('user_session', function (Blueprint $table) {
            $table->id();
            $table->string('shortname')->index();
            $table->string('session_id')->index();
            $table->string('username')->index();
            $table->datetime('start')->nullable();
            $table->datetime('stop')->nullable();
            $table->datetime('update')->nullable();
            $table->string('nas_address')->index();
            $table->string('ip')->nullable();
            $table->string('mac')->nullable();
            $table->bigInteger('input')->nullable();
            $table->bigInteger('output')->nullable();
            $table->bigInteger('uptime')->nullable();
            $table->integer('type');
            $table->integer('status')->default('1');
            $table->string('AcctUniqueId')->unique();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_session');
    }
};
