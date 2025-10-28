<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $typeParam = $request->input('type'); 

            $authUser = $request->user();

            if ($typeParam) {
                $companyType = $typeParam;
            } else {
                $companyType = tenancy()->tenant->type === 'admin'
                    ? 'admin'
                    : ($authUser->partner_id ? 'partner' : 'organization');
            }

            $query = Role::where('company_type', $companyType);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $roles = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Lista de roles',
                'data' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
