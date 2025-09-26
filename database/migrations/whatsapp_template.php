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
        Schema::connection('mysql')->create('whatsapp_template', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->longText('invoice_terbit');
            $table->longText('invoice_reminder');
            $table->longText('invoice_overdue');
            $table->longText('payment_paid');
            $table->longText('payment_cancel');
            $table->longText('account_active');
            $table->longText('account_suspend');
            $table->longText('tiket_open_pelanggan');
            $table->longText('tiket_close_pelanggan');
            $table->longText('tiket_open_teknisi');
            $table->longText('tiket_close_teknisi');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_template');
    }
};
