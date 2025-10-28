<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class CreateTenantAdmin extends Command
{
    protected $signature = 'setup:admin-tenant';
    protected $description = 'Criar tenant de admin';

    public function handle()
    {
        $this->info('Iniciando setup de tenant...');

        $tenant = Tenant::create([
            'id' => 'gruposcale',
            'data' => ['database' => 'gruposcale'],
            'type' => 'admin'
        ]);

        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
            '--force' => true,
        ]);

        $tenant->run(function () {
            User::create([
                'name' => 'Admin',
                'email' => env('TENANT_USER_ADMIN','admin@gruposcale.com.br'),
                'password' => Hash::make(env('TENANT_ADMIN_PASSWORD', 'GrupoScale!@123')),
                'role_id' => 1
            ]);

            Organization::create([
                'description' => 'Grupo Scale',
                'cpf_cnpj' => '00.000.000/0000-00',
                'email' => 'contato@gruposcale.com',
                'phone' => '(00) 00000-0000',
                'active' => 1,
                'logo' => null, 
            ]);
        });

        $this->info('Tenant e organização criados com sucesso!');
        return true;
    }
}
