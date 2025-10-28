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
        if (!Schema::hasTable('daily_registers')) {
            Schema::create('daily_registers', function (Blueprint $table) {
                $table->id();
                $table->integer('partner_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->date('date')->nullable();
                $table->integer('step_one')->nullable();
                $table->integer('step_two')->nullable();
                $table->integer('step_three')->nullable();
                $table->integer('step_four')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_registers');
    }
};
