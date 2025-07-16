<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'numero_sessao' => 'required|integer|min:1',
            'fase' => 'nullable|string|max:50',
            'tipo' => 'nullable|string|max:100',
            'data_agendada' => 'nullable|date',
            'data_realizada' => 'nullable|date',
            'status' => 'required|in:agendada,realizada,cancelada,reagendada',
            'observacoes' => 'nullable|string',
            'resultado' => 'nullable|string'
        ]);

        $session = Session::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sessão criada com sucesso!',
            'data' => $session
        ]);
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sessão excluída com sucesso!'
        ]);
    }
}
