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
        Schema::connection('mysql')->create('setting_company', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('name');
            $table->string('singkatan')->nullable();
            $table->string('slogan')->nullable();
            $table->string('email')->nullable();
            $table->string('wa')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('bank')->nullable();
            $table->string('holder')->nullable();
            $table->string('note')->nullable();
            $table->string('logo')->nullable();
            $table->string('group_ggn')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_company');
    }
};
