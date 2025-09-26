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
        Schema::create('billing_service', function (Blueprint $table) {
            $table->id();
            $table->integer('dinetkan_user_id');
            $table->integer('total_price')->default(0);
            $table->integer('total_ppn')->default(0);
            $table->integer('total_bhp')->default(0);
            $table->integer('total_uso')->default(0);
            $table->integer('total_member')->default(0);
            $table->string('status')->default('unpaid');
            $table->string('notes');
            $table->string('paid_via');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_service');
    }
};
