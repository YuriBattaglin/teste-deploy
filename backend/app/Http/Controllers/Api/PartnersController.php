<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Partner;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class PartnersController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Partner::withCount('users')
                ->with('squad');

            if ($request->filled('search')) {
                $query->where('description', 'like', "%{$request->input('search')}%");
            }

            if ($request->filled('active')) {
                $query->where('active', '=', "{$request->input('active')}");
            }

            if ($request->filled('limit')) {
                $partners = $query->limit((int)$request->input('limit'))->get();
            } else {
                $perPage = (int) $request->input('per_page', 10);
                $partners = $query->paginate($perPage);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lista de parceiros',
                'data' => $partners
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar parceiros',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $logoBase64 = null;
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoBase64 = base64_encode(file_get_contents($request->file('logo')->getRealPath()));
            }
            if ($request->squad_id === 'null' || $request->squad_id === '') {
                $request->merge(['squad_id' => null]);
            }

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'cpf_cnpj' => 'required|string',
                'squad_id' => 'nullable|integer',
                'phone' => 'nullable|string',
                'active' => 'sometimes|boolean',
                'logo' => 'nullable|file|image',
                'firstStep' => 'required|nullable|string',
                'secondStep' => 'required|nullable|string',
                'thirdStep' => 'required|nullable|string',
                'fourthStep' => 'required|nullable|string'
            ]);

            $steps = [
                $request->firstStep,
                $request->secondStep,
                $request->thirdStep,
                $request->fourthStep
            ];

            $stepsFiltered = array_filter($steps);
            if (count($stepsFiltered) !== count(array_unique($stepsFiltered))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Os valores de steps não podem se repetir.'
                ], 422);
            }

            $partner = Partner::create([
                'description' => $request->name,
                'squad_id' => $request->squad_id,
                'cpf_cnpj' => $request->cpf_cnpj ?? '00.000.000/0000-00',
                'email' => $request->email,
                'phone' => $request->phone ?? null,
                'active' => $request->active ? 1 : 0,
                'logo' => $logoBase64,
            ]);

            Metric::create([
                'partner_id' => $partner->id,
                'step_one' => $request->firstStep,
                'step_two' => $request->secondStep,
                'step_three' => $request->thirdStep,
                'step_four' => $request->fourthStep
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parceiro criado com sucesso',
                'data' => ['id' => $partner->id]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar parceiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $partner = Partner::with(['users:id,partner_id,name,email,created_at', 'metrics'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Parceiro encontrado',
                'data' => [
                    'id' => $partner->id,
                    'name' => $partner->description ?? null,
                    'squad_id' => $partner->squad_id ?? null,
                    'cpf_cnpj' => $partner->cpf_cnpj ?? null,
                    'email' => $partner->email ?? null,
                    'phone' => $partner->phone ?? null,
                    'active' => $partner->active ?? true,
                    'logo_base64' => $partner->logo ?? null,
                    'users_count' => $partner->users->count(),
                    'users' => $partner->users,
                    'metrics' => $partner->metrics,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Parceiro não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
          if ($request->squad_id === 'null' || $request->squad_id === '') {
                $request->merge(['squad_id' => null]);
            }
        $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'cpf_cnpj' => 'sometimes|required|string',
            'phone' => 'nullable|string',
            'active' => 'sometimes|boolean',
            'logo' => 'nullable|file|image',
            'secondStep' => 'required|nullable|string',
            'thirdStep' => 'required|nullable|string',
        ]);


        $steps = [
            $request->firstStep,
            $request->secondStep,
            $request->thirdStep,
            $request->fourthStep
        ];

        $stepsFiltered = array_filter($steps);
        if (count($stepsFiltered) !== count(array_unique($stepsFiltered))) {
            return response()->json([
                'success' => false,
                'message' => 'Os valores de steps não podem se repetir.'
            ], 422);
        }

        try {
            $partner = Partner::findOrFail($id);
            $partner->description = $request->has('name') ? $request->name : $partner->description;
            $partner->cpf_cnpj   = $request->has('cpf_cnpj') ? $request->cpf_cnpj : $partner->cpf_cnpj;
            $partner->email      = $request->has('email') ? $request->email : $partner->email;
            $partner->phone      = $request->has('phone') ? $request->phone : $partner->phone;
            $partner->active     = $request->has('active') ? ($request->active ? 1 : 0) : $partner->active;
            $partner->squad_id   = $request->has('squad_id') ? $request->squad_id : $partner->squad_id;

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $partner->logo = base64_encode(file_get_contents($request->file('logo')->getRealPath()));
            }

            $updateData = [];
            if ($request->filled('secondStep')) {
                $updateData['step_two'] = $request->secondStep;
            }
            if ($request->filled('thirdStep')) {
                $updateData['step_three'] = $request->thirdStep;
            }
            if (!empty($updateData)) {
                Metric::where('partner_id', $id)->update($updateData);
            }

            $partner->save();

            return response()->json([
                'success' => true,
                'message' => 'Parceiro atualizado com sucesso',
                'data' => [
                    'id' => $partner->id,
                    'name' => $partner->description,
                    'cpf_cnpj' => $partner->cpf_cnpj,
                    'email' => $partner->email,
                    'phone' => $partner->phone,
                    'active' => $partner->active,
                    'squad_id' => $partner->squad_id,
                    'logo_base64' => $partner->logo,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar parceiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $partner = Partner::findOrFail($id);

            if ($partner->metrics) {
                $partner->metrics()->delete();
            }

            if ($partner->users) {
                $partner->users()->delete();
            }

            $partner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Parceiro removido com sucesso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover parceiro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
