<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traffic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TrafficsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $query = Traffic::query();
            $query->orderBy('id', 'desc');

            $startDate = $request->input('start_date');
            $endDate   = $request->input('end_date');
            $partner   = $request->input('partner_id');
            if ($startDate && $endDate) {
                $query->where('start_date', $startDate)
                    ->where('end_date', $endDate);
            }
            if ($partner) {
                $query->where('partner_id', $partner);
            }

            $traffic = $query->paginate($perPage)->through(function (Traffic $row) {
                return [
                    'id'             => $row->id,
                    'partner'        => $row->partner,
                    'impressions'    => $row->impressions,
                    'clicks'         => $row->clicks,
                    'ad_investment'  => $row->ad_investment,
                    'leads'          => $row->leads,
                    'observation'    => $row->observation,
                    'start_date'     => $row->start_date,
                    'end_date'       => $row->end_date,
                    'created_at'     => $row->created_at,
                    'updated_at'     => $row->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lista de registros de tráfego',
                'data'    => $traffic,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar tráfego',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'partner_id'    => 'required|integer|exists:partners,id',
            'impressions'   => 'required|integer|min:0',
            'clicks'        => 'required|integer|min:0',
            'ad_investment' => 'required|numeric|min:0',
            'leads'         => 'required|integer|min:0',
            'observation'   => 'nullable|string|max:65535',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $traffic = Traffic::find($id);

            if (!$traffic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro de tráfego não encontrado',
                ], 404);
            }

            $traffic->update([
                'partner_id'    => $request->input('partner_id'),
                'impressions'   => $request->input('impressions'),
                'clicks'        => $request->input('clicks'),
                'ad_investment' => $request->input('ad_investment'),
                'leads'         => $request->input('leads'),
                'observation'   => $request->input('observation'),
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro de tráfego atualizado com sucesso',
                'data'    => $traffic
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar registro de tráfego',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id'    => 'required|integer|exists:partners,id',
            'impressions'   => 'required|integer|min:0',
            'clicks'        => 'required|integer|min:0',
            'ad_investment' => 'required|numeric|min:0',
            'leads'         => 'required|integer|min:0',
            'observation'   => 'nullable|string|max:65535',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $traffic = Traffic::create([
                'partner_id'    => $request->input('partner_id'),
                'impressions'   => $request->input('impressions'),
                'clicks'        => $request->input('clicks'),
                'ad_investment' => $request->input('ad_investment'),
                'leads'         => $request->input('leads'),
                'observation'   => $request->input('observation'),
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registro de tráfego criado com sucesso',
                'data'    => $traffic
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar registro de tráfego',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
