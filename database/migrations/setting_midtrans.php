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
        Schema::connection('mysql')->create('setting_midtrans', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('id_merchant')->nullable();
            $table->string('client_key')->nullable();
            $table->string('server_key')->nullable();
            $table->string('webhook_url')->nullable();
            $table->integer('admin_fee')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_midtrans');
    }
};
