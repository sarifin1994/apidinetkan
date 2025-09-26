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
        Schema::connection('mysql')->create('keuangan_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('category');
            $table->string('type'); // Pemasukan // Pengeluaran
            $table->integer('status')->default(1); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_kategori');
    }
};
