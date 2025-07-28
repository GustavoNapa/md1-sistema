<?php

namespace App\Http\Controllers;

use App\Models\Faturamento;
use App\Models\Inscription;
use Illuminate\Http\Request;

class FaturamentoController extends Controller
{
    public function index(Inscription $inscription)
    {
        $faturamentos = $inscription->faturamentos()->orderBy('mes_ano', 'desc')->get();
        return response()->json($faturamentos);
    }

    public function store(Request $request, Inscription $inscription)
    {
        $request->validate([
            'mes_ano' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'status' => 'required|string|in:pendente,pago,vencido,cancelado',
            'observacoes' => 'nullable|string'
        ]);

        $faturamento = $inscription->faturamentos()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Faturamento criado com sucesso!',
            'data' => $faturamento
        ], 201);
    }

    public function show(Inscription $inscription, Faturamento $faturamento)
    {
        if ($faturamento->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Faturamento não encontrado'], 404);
        }

        return response()->json($faturamento);
    }

    public function update(Request $request, Inscription $inscription, Faturamento $faturamento)
    {
        if ($faturamento->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Faturamento não encontrado'], 404);
        }

        $request->validate([
            'mes_ano' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'status' => 'required|string|in:pendente,pago,vencido,cancelado',
            'observacoes' => 'nullable|string'
        ]);

        $faturamento->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Faturamento atualizado com sucesso!',
            'data' => $faturamento
        ]);
    }

    public function destroy(Inscription $inscription, Faturamento $faturamento)
    {
        if ($faturamento->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Faturamento não encontrado'], 404);
        }

        $faturamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faturamento excluído com sucesso!'
        ]);
    }
}

