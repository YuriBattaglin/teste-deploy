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
        if (!Schema::hasTable('squads')) {
            Schema::create('squads', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('leader_id')->nullable();
                $table->boolean('active')->nullable()->default(true);
                $table->text('description');
                $table->json('members')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('squads')) {
            Schema::dropIfExists('squads');
        }
    }
};
