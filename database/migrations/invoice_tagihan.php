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
        Schema::connection('mysql')->create('invoice', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->string('id_pelanggan');
            $table->string('no_invoice')->unique();
            $table->string('item');
            $table->integer('price')->default('0');
            $table->integer('ppn')->nullable();
            $table->integer('discount')->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('period');
            $table->string('subscribe');
            $table->string('payment_type');
            $table->string('billing_period');
            $table->string('payment_url')->nullable();
            $table->string('snap_token')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('status');
            $table->integer('mitra_id')->nullable(); // 0 system null system
            $table->integer('komisi')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
