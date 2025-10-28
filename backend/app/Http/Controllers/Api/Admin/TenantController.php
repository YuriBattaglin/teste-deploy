<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Organization;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Artisan;

class TenantController extends Controller
{
   public function index(Request $request)
{
    try {
        $perPage = (int) $request->input('per_page', 10);

        $tenants = Tenant::where('type', '!=', 'admin')->paginate($perPage);

        $tenantsData = $tenants->getCollection()->map(function ($tenant) use ($request) {
            Tenancy::initialize($tenant);

            $organization = Organization::first();
            if (!$organization) {
                return null;
            }

            if ($request->filled('active')) {
                $activeFilter = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if ((bool) $organization->active !== $activeFilter) {
                    return null;
                }
            }

            if ($request->filled('search')) {
                $search = mb_strtolower($request->input('search'));
                $orgName = mb_strtolower($organization->description ?? '');
                if (!str_contains($orgName, $search)) {
                    return null;
                }
            }

            $usersCount = User::count();

            return [
                'tenant_id' => $tenant->id,
                ...$organization->toArray(),
                'users_count' => $usersCount,
            ];
        })
        ->filter() 
        ->values(); 

        $tenants->setCollection($tenantsData);

        return response()->json([
            'success' => true,
            'message' => 'Lista de tenants',
            'data' => $tenants
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao buscar tenants',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|unique:tenants,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'cpf_cnpj' => 'required|string',
            'phone' => 'nullable|string',
            'active' => 'required|boolean',
            'logo' => 'nullable'
        ]);

        try {
            $tenant = Tenant::create([
                'id' => $request->id,
                'data' => ['database' => $request->id],
                'type' => 'organization'
            ]);

            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
                '--force' => true,
            ]);

                Artisan::call('tenants:seed', [
                '--tenants' => [$tenant->id],
                '--force' => true,
            ]);
            $tenant->run(function () use ($request) {
                $logoBase64 = null;
                if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                    $logoBase64 = base64_encode(file_get_contents($request->file('logo')->getRealPath()));
                }

                Organization::create([
                    'description' => $request->name,
                    'cpf_cnpj' => $request->cpf_cnpj ?? '00.000.000/0000-00',
                    'email' => $request->email,
                    'phone' => $request->phone ?? null,
                    'active' => $request->active ? 1 : 0,
                    'logo' => $logoBase64,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tenant criado com sucesso',
                'data' => ['tenant_id' => $tenant->id]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            Tenancy::initialize($tenant);

            $organization = Organization::first();
            $users = User::select('id', 'name', 'email', 'created_at')->get();

            return response()->json([
                'success' => true,
                'message' => 'Tenant encontrado',
                'data' => [
                    'id' => $tenant->id,
                    'name' => $organization->description ?? null,
                    'cpf_cnpj' => $organization->cpf_cnpj ?? null,
                    'email' => $organization->email ?? null,
                    'phone' => $organization->phone ?? null,
                    'active' => $organization->active ?? true,
                    'logo_base64' => $organization->logo ?? null,
                    'users_count' => $users->count(),
                    'users' => $users
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'cpf_cnpj' => 'sometimes|string',
            'phone' => 'nullable|string',
            'active' => 'sometimes|boolean',
            'logo' => 'nullable|file|image'
        ]);

        try {
            $tenant = Tenant::findOrFail($id);

            Tenancy::initialize($tenant);

            $organization = Organization::firstOrFail();
            $organization->description = $request->has('name') ? $request->name : $organization->description;
            $organization->cpf_cnpj   = $request->has('cpf_cnpj') ? $request->cpf_cnpj : $organization->cpf_cnpj;
            $organization->email      = $request->has('email') ? $request->email : $organization->email;
            $organization->phone      = $request->has('phone') ? $request->phone : $organization->phone;
            $organization->active     = $request->has('active') ? ($request->active ? 1 : 0) : $organization->active;

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $organization->logo = base64_encode(file_get_contents($request->file('logo')->getRealPath()));
            }

            $organization->save();

            return response()->json([
                'success' => true,
                'message' => 'Tenant atualizado com sucesso',
                'data' => [
                    'id' => $tenant->id,
                    'name' => $organization->description,
                    'cpf_cnpj' => $organization->cpf_cnpj,
                    'email' => $organization->email,
                    'phone' => $organization->phone,
                    'active' => $organization->active,
                    'logo_base64' => $organization->logo,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
