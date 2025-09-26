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
        Schema::connection('mysql')->create('setting_duitku', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('id_merchant')->nullable();
            $table->string('api_key')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('return_url')->nullable();
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
        Schema::dropIfExists('setting_duitku');
    }
};
