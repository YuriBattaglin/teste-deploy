<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Squad;
use Illuminate\Support\Facades\Validator;

class SquadsController extends Controller
{
   public function index(Request $request)
{
    try {
        $perPage = (int) $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Squad::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

         if ($request->filled('active')) {
                $query->where('active', '=', "{$request->input('active')}");
            }

        $squads = $query->paginate($perPage)->through(function ($squad) {
            return [
                'id' => $squad->id,
                'name' => $squad->name,
                'description' => $squad->description,
                'active' => $squad->active,
                'leader' => $squad->leader ? $squad->leader->name : null,
                'members' => $squad->membersList()->pluck('name'),
                'members_count' => count($squad->members ?? []),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Lista de squads',
            'data' => $squads
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao listar squads',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function store(Request $request)
    {
        if (isset($request->members) && is_string($request->members)) {
            $request->merge([
                'members' => json_decode($request->members, true)
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'leader_id' => 'required|exists:users,id',
            'active' => 'boolean',
            'description' => 'required|string|max:65535',
            'members' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $squad = Squad::create([
                'name' => $request->name,
                'leader_id' => $request->leader_id,
                'active' => $request->has('active') ? $request->active : true,
                'description' => $request->description,
                'members' => $request->members ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Squad criado com sucesso',
                'data' => $squad
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar squad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $squad = Squad::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Squad encontrado',
                'data' =>  [
                    'id' => $squad->id,
                    'name' => $squad->name,
                    'description' => $squad->description,
                    'active' => $squad->active,
                    'leader_id' => $squad->leader ? $squad->leader->id : null,
                    'members' => $squad->membersList(),
                    'partners' => $squad->partners,
                    'members_count' => count($squad->members ?? []),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Squad não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }


public function update(Request $request, $id)
{
    if (isset($request->members) && is_string($request->members)) {
        $request->merge([
            'members' => json_decode($request->members, true)
        ]);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'leader_id' => 'nullable|exists:users,id',
        'active' => 'boolean',
        'description' => 'nullable|string|max:65535',
        'members' => 'nullable|array'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $squad = Squad::findOrFail($id);
        $squad->update([
            'name' => $request->has('name') ? $request->name : $squad->name,
            'leader_id' => $request->has('leader_id') ? $request->leader_id : $squad->leader_id,
            'active' => $request->has('active') ? $request->active : $squad->active,
            'description' => $request->has('description') ? $request->description : $squad->description,
            'members' => $request->has('members') ? $request->members : $squad->members
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Squad atualizado com sucesso',
            'data' => $squad
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar squad',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function destroy($id)
    {
        try {
            $squad = Squad::findOrFail($id);
            $squad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Squad removido com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover squad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
