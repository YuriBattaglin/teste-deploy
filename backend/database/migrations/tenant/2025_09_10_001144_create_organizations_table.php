<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->string('description'); 
                $table->string('cpf_cnpj')->unique();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->longText('logo')->nullable();
                $table->boolean('active')->nullable()->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

