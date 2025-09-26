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
        Schema::connection('mysql')->create('hotspot_profile', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('name');
            $table->string('price')->default(0);
            $table->string('reseller_price')->default(0);
            $table->string('rateLimit')->nullable();
            $table->string('quota')->default('Unlimited');
            $table->string('uptime')->default('Unlimited');
            $table->string('validity')->default('Unlimited');
            $table->integer('shared');
            $table->integer('mac'); // 0 lock disable // 1 lock enable
            $table->string('groupProfile')->nullable();
            $table->integer('status')->default('1'); // 1 aktif // 2 nonaktif
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_profile');
    }
};
