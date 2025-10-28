<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('entrou aqui 2');
        $tenantId = $request->header('X-Tenant');

        if (!$tenantId) {
            return response()->json(['success' => false, 'message' => 'Tenant não informado'], 400);
        }

        // Buscar tenant no banco landlord
        $tenant = Tenant::where('id', $tenantId)->first();

        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant inválido'], 404);
        }

        // Setar o tenant ativo
        tenancy()->initialize($tenant);

        return $next($request);
    }
}
