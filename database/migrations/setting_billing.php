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
        Schema::connection('mysql')->create('setting_billing', function (Blueprint $table) {
            $table->id();
            $table->string('shortname');
            $table->integer('due_bc');
            $table->integer('inv_fd');
            $table->integer('suspend_date');
            $table->string('suspend_time');
            $table->integer('notif_ir');
            $table->integer('notif_it');
            $table->integer('notif_ps');
            $table->integer('notif_sm');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_billing');
    }
};
