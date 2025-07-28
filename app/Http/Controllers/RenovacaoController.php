<?php

namespace App\Http\Controllers;

use App\Models\Renovacao;
use App\Models\Inscription;
use Illuminate\Http\Request;

class RenovacaoController extends Controller
{
    public function index(Inscription $inscription)
    {
        $renovacoes = $inscription->renovacoes()->orderBy('data_inicio', 'desc')->get();
        return response()->json($renovacoes);
    }

    public function store(Request $request, Inscription $inscription)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'valor' => 'required|numeric|min:0',
            'status' => 'required|string|in:pendente,aprovada,rejeitada,cancelada',
            'observacoes' => 'nullable|string'
        ]);

        $renovacao = $inscription->renovacoes()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Renovação criada com sucesso!',
            'data' => $renovacao
        ], 201);
    }

    public function show(Inscription $inscription, Renovacao $renovacao)
    {
        if ($renovacao->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Renovação não encontrada'], 404);
        }

        return response()->json($renovacao);
    }

    public function update(Request $request, Inscription $inscription, Renovacao $renovacao)
    {
        if ($renovacao->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Renovação não encontrada'], 404);
        }

        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'valor' => 'required|numeric|min:0',
            'status' => 'required|string|in:pendente,aprovada,rejeitada,cancelada',
            'observacoes' => 'nullable|string'
        ]);

        $renovacao->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Renovação atualizada com sucesso!',
            'data' => $renovacao
        ]);
    }

    public function destroy(Inscription $inscription, Renovacao $renovacao)
    {
        if ($renovacao->inscription_id !== $inscription->id) {
            return response()->json(['error' => 'Renovação não encontrada'], 404);
        }

        $renovacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Renovação excluída com sucesso!'
        ]);
    }
}

