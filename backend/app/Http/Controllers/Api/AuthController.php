<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Facades\Tenancy;

class AuthController extends Controller
{
    /**
     * Login do usuário multi-tenant
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tenant = Tenant::find($request->tenant);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            Tenancy::initialize($tenant);

            $ttl = $request->remember ? 60 * 24 * 30 : config('jwt.ttl');
            auth()->factory()->setTTL($ttl);

            $token = Auth::attempt($request->only('email', 'password'));
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ])->withoutCookie('auth_token');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   public function me(Request $request)
{
    try {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $tenant = tenancy()->tenant;

        // carrega parceiro se existir
        $partner = null;
        if ($user->partner_id) {
            $partner = Partner::with('metrics')->find($user->partner_id);
        }

         $organization = Organization::first(); 

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_type' => $user->role->access_type,
                'organization' => $organization ? [
                    'id' => $organization->id,
                    'name' => $organization->description,
                    'cpf_cnpj' => $organization->cpf_cnpj,
                    'email' => $organization->email,
                    'phone' => $organization->phone,
                    'active' => $organization->active,
                    'logo' => $organization->logo,
                ] : null,
                'partner' => $partner ? [
                    'id' => $partner->id,
                    'name' => $partner->description,
                    'cpf_cnpj' => $partner->cpf_cnpj,
                    'email' => $partner->email,
                    'phone' => $partner->phone,
                    'active' => $partner->active,
                    'logo' => $partner->logo,
                    'metrics' => $partner->metrics
                ] : null,
                'tenant' => [
                    'id' => $tenant->id,
                    'type' => $tenant->type,
                ]
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao obter informações do usuário',
            'error' => $e->getMessage()
        ], 500);
    }
}


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
