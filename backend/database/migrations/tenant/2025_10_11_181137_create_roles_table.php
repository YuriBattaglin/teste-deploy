<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->string('name');
                $table->integer('access_type');
                $table->string('company_type');
                $table->timestamps();
            });

            DB::table('roles')->insert([
                ['id' => 1, 'name' => 'Administrador', 'access_type' => 1, 'company_type' => 'admin', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'Administrador', 'access_type' => 1, 'company_type' => 'organization', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'Gestor', 'access_type' => 2, 'company_type' => 'organization', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'name' => 'Head', 'access_type' => 3, 'company_type' => 'organization', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 5, 'name' => 'Estrategista', 'access_type' => 4, 'company_type' => 'organization', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 6, 'name' => 'Administrador', 'access_type' => 1, 'company_type' => 'partner', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 7, 'name' => 'Gerente', 'access_type' => 2, 'company_type' => 'partner', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 8, 'name' => 'Vendedor', 'access_type' => 3, 'company_type' => 'partner', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 9, 'name' => 'Pré-vendedor', 'access_type' => 4, 'company_type' => 'partner', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
