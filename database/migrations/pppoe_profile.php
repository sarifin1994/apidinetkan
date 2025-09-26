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
        Schema::connection('mysql')->create('pppoe_profile', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('name');
            $table->string('price')->default(0);
            $table->string('fee_mitra')->default(0);
            $table->string('rateLimit')->nullable();
            $table->string('groupProfile')->nullable();
            $table->string('validity')->default('Unlimited');
            $table->integer('status')->default('1'); // 1 aktif // 2 nonaktif
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pppoe_profile');
    }
};
