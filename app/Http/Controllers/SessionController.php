<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function show(Session $session)
    {
        $session->load('preceptorRecord');
        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'preceptor_record_id' => 'required|exists:preceptor_records,id',
            'numero_sessao' => 'required|integer|min:1',
            'fase' => 'required|string|max:50',
            'semana_mes' => 'required|integer|min:1|max:5',
            'tipo' => 'nullable|string|max:100',
            'data_agendada' => 'nullable|date',
            'data_realizada' => 'nullable|date',
            'status' => 'required|in:agendada,realizada,cancelada,reagendada,no_show',
            'confirmou_24h' => 'required|boolean',
            'medico_confirmou' => 'required|in:confirmou,desmarcou,nao_respondeu',
            'motivo_desmarcou' => 'nullable|string',
            'medico_compareceu' => 'required|boolean',
            'status_reagendamento' => 'nullable|in:reagendado,em_processo,sem_comunicacao',
            'data_remarcada' => 'nullable|date',
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

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'preceptor_record_id' => 'required|exists:preceptor_records,id',
            'numero_sessao' => 'required|integer|min:1',
            'fase' => 'required|string|max:50',
            'semana_mes' => 'required|integer|min:1|max:5',
            'tipo' => 'nullable|string|max:100',
            'data_agendada' => 'nullable|date',
            'data_realizada' => 'nullable|date',
            'status' => 'required|in:agendada,realizada,cancelada,reagendada,no_show',
            'confirmou_24h' => 'required|boolean',
            'medico_confirmou' => 'required|in:confirmou,desmarcou,nao_respondeu',
            'motivo_desmarcou' => 'nullable|string',
            'medico_compareceu' => 'required|boolean',
            'status_reagendamento' => 'nullable|in:reagendado,em_processo,sem_comunicacao',
            'data_remarcada' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'resultado' => 'nullable|string'
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sessão atualizada com sucesso!',
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
