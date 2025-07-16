<?php

namespace App\Http\Controllers;

use App\Models\PreceptorRecord;
use Illuminate\Http\Request;

class PreceptorRecordController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'nome_preceptor' => 'required|string|max:255',
            'historico_preceptor' => 'nullable|string',
            'data_preceptor_informado' => 'nullable|date',
            'data_preceptor_contato' => 'nullable|date',
            'nome_secretaria' => 'nullable|string|max:255',
            'email_clinica' => 'nullable|email|max:255',
            'whatsapp_clinica' => 'nullable|string|max:20',
            'usm' => 'boolean',
            'acesso_vitrine_gmc' => 'boolean',
            'medico_celebridade' => 'boolean'
        ]);

        $preceptorRecord = PreceptorRecord::create($validated);

        // Garantir que sempre retorna JSON para requisições AJAX
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro de preceptor criado com sucesso!',
                'data' => $preceptorRecord
            ]);
        }
        
        // Fallback para requisições normais (não deveria acontecer)
        return redirect()->back()->with('success', 'Registro de preceptor criado com sucesso!');
    }

    public function destroy(PreceptorRecord $preceptorRecord)
    {
        $preceptorRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de preceptor excluído com sucesso!'
        ]);
    }
}
