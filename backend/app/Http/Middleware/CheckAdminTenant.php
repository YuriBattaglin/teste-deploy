<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $adminTenantId = env('TENANT_ADMIN', 'gruposcale');
        $tenantId = $request->header('X-Tenant');

        if (!$tenantId || $tenantId !== $adminTenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado: apenas tenant admin permitido'
            ], 403);
        }

        return $next($request);
    }
}
