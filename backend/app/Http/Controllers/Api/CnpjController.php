<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class CnpjController extends Controller
{
    public function show(string $cnpj): JsonResponse
    {
        try {
            $response = Http::withoutVerifying()->get("https://www.receitaws.com.br/v1/cnpj/{$cnpj}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dados do CNPJ encontrados',
                    'data' => $response->json(),
                ], 200);
            }
            throw new \Exception('Erro ao buscar dados do CNPJ');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados do CNPJ',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
