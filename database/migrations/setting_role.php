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
        Schema::connection('mysql')->create('setting_role', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->integer('teknisi_status_regist')->default(0);
            $table->integer('kasir_melihat_total_keuangan')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_role');
    }
};
