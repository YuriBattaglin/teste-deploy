<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DailyRegistersController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $query = DailyRegister::query();
            $query->orderBy('id', 'desc');

            $date = $request->input('date');
            $partner   = $request->input('partner_id');

            if ($date) {
                $query->where('date', $date);
            }

            if ($partner) {
                $query->where('partner_id', $partner);
            }

            $registers = $query->paginate($perPage)->through(function (DailyRegister $row) {
                return [
                    'id'         => $row->id,
                    'partner' => $row->partner,
                    'user' => $row->user,
                    'date'       => $row->date,
                    'step_one'   => $row->step_one,
                    'step_two'   => $row->step_two,
                    'step_three' => $row->step_three,
                    'step_four'  => $row->step_four,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lista de registros diários',
                'data'    => $registers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar registros diários',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|integer|exists:partners,id',
            'user_id' => 'required|integer|exists:users,id',
            'date'       => 'required|date',
            'step_one'   => 'nullable|integer|min:0',
            'step_two'   => 'nullable|integer|min:0',
            'step_three' => 'nullable|integer|min:0',
            'step_four'  => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors'  => $validator->errors()
            ], 422);
        }
        $request->merge([
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
        ]);
        try {
            $register = DailyRegister::create([
                'partner_id' => $request->input('partner_id'),
                'user_id' => $request->input('user_id'),
                'date'       => $request->input('date'),
                'step_one'   => $request->input('step_one'),
                'step_two'   => $request->input('step_two'),
                'step_three' => $request->input('step_three'),
                'step_four'  => $request->input('step_four'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro diário criado com sucesso',
                'data'    => $register
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar registro diário',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|integer|exists:partners,id',
            'user_id' => 'required|integer|exists:users,id',
            'date'       => 'required|date',
            'step_one'   => 'nullable|integer|min:0',
            'step_two'   => 'nullable|integer|min:0',
            'step_three' => 'nullable|integer|min:0',
            'step_four'  => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $register = DailyRegister::find($id);

            if (!$register) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro diário não encontrado',
                ], 404);
            }
 $request->merge([
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
        ]);
            $register->update([
                'partner_id' => $request->input('partner_id'),
                'user_id' => $request->input('user_id'),
                'date'       => $request->input('date'),
                'step_one'   => $request->input('step_one'),
                'step_two'   => $request->input('step_two'),
                'step_three' => $request->input('step_three'),
                'step_four'  => $request->input('step_four'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro diário atualizado com sucesso',
                'data'    => $register
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar registro diário',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $register = DailyRegister::find($id);

            if (!$register) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro diário não encontrado',
                ], 404);
            }

            $register->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registro diário excluído com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir registro diário',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
