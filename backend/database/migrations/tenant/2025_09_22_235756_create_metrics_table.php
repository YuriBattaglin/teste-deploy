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
        if (!Schema::hasTable('metrics')) {
            Schema::create('metrics', function (Blueprint $table) {
                $table->id();
                $table->integer('partner_id')->nullable();
                $table->string('step_one');
                $table->string('step_two');
                $table->string('step_three');
                $table->string('step_four');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('metrics')) {
            Schema::dropIfExists('metrics');
        }
    }
};
