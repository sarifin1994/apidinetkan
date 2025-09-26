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
        Schema::connection('frradius_auth')->create('user_pppoe', function (Blueprint $table) {
            $table->id();
            $table->string('shortname')->index();
            $table->string('username')->index();
            $table->string('attribute')->default('Cleartext-Password');
            $table->string('op')->default(':=');
            $table->string('value');
            $table->string('profile');
            $table->string('nas')->nullable();
            $table->string('service')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('status'); // 1 aktif // 2 isolir/nonaktif // 3 disabled
            $table->string('type')->default('pppoe');
            $table->integer('lock_mac');
            $table->string('mac')->nullable();

            $table->string('id_pelanggan')->unique();
            $table->integer('profile_id');
            $table->integer('mitra_id')->default(0);
            $table->string('kode_area')->nullable();
            $table->string('kode_odp')->nullable();
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->enum('payment_type', ['Pascabayar', 'Prabayar'])->nullable();
            $table->enum('billing_period',['Billing Cycle','Fixed Date','Renewable'])->nullable();
            $table->integer('ppn')->nullable();
            $table->integer('discount')->nullable();
            $table->date('reg_date')->nullable();
            $table->date('next_due')->nullable();
            $table->date('next_invoice')->nullable();
            $table->string('tgl')->nullable();
            $table->string('wa')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pppoe');
    }
};
