<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;

class UsersController extends Controller
{
    protected function initTenant($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        Tenancy::initialize($tenant);
        return $tenant;
    }

public function index(Request $request, $tenantId)
{
    try {
        $this->initTenant($tenantId);

        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $query = User::whereNull('partner_id');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao buscar usuários',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function store(Request $request, $tenantId)
    {
        $this->initTenant($tenantId);
       
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Usuário criado com sucesso', 'data' => $user], 201);
    }

    public function show($tenantId, $id)
    {
        $this->initTenant($tenantId);

        $user = User::findOrFail($id);
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, $tenantId, $id)
    {
        $this->initTenant($tenantId);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['message' => 'Usuário atualizado com sucesso', 'data' => $user]);
    }

    public function destroy($tenantId, $id)
    {
        $this->initTenant($tenantId);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuário removido com sucesso']);
    }
}
