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
        if (!Schema::hasTable('traffic')) {
            Schema::create('traffic', function (Blueprint $table) {
                $table->id();
                $table->integer('partner_id')->nullable();
                $table->integer('impressions')->nullable();
                $table->integer('clicks')->nullable();
                $table->double('ad_investment')->nullable();
                $table->integer('leads')->nullable();
                $table->text('observation')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();

                // Optional foreign key
                // $table->foreign('partner_id')->references('id')->on('partners')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('traffic')) {
            Schema::dropIfExists('traffic');
        }
    }
};
