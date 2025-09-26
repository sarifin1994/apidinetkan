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
        Schema::connection('frradius_auth')->create('user_hs', function (Blueprint $table) {
            $table->id();
            $table->string('shortname')->index();
            $table->string('username')->index();
            $table->string('attribute')->default('Cleartext-Password');
            $table->string('op')->default(':=');
            $table->string('value');
            $table->string('profile');
            $table->string('nas')->nullable();
            $table->string('server')->nullable();
            $table->integer('status'); // 1 aktif // 2 isolir // 3 off
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->string('remark')->nullable();
            $table->integer('reseller_id')->nullable();
            $table->integer('statusPayment');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_hs');
    }
};
