<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Capturar o tenant do cabeçalho
        $tenantId = $request->header('X-Tenant-ID');
        
        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant ID é obrigatório no cabeçalho X-Tenant-ID'
            ], 400);
        }

        try {
            // Se o tenant for "admin", não inicializar contexto específico
            // Isso permitirá acesso a todos os dados
            if ($tenantId === 'admin') {
                // Adicionar informação do tenant admin na requisição
                $request->merge(['is_admin_tenant' => true]);
                return $next($request);
            }

            // Buscar o tenant
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant não encontrado'
                ], 404);
            }

            // Inicializar o contexto do tenant
            Tenancy::initialize($tenant);
            
            // Adicionar informação do tenant na requisição
            $request->merge([
                'current_tenant' => $tenant,
                'is_admin_tenant' => false
            ]);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
