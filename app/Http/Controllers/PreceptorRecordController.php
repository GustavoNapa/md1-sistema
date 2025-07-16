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
            'crm' => 'nullable|string|max:20',
            'especialidade' => 'nullable|string|max:255',
            'hospital' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string'
        ]);

        $preceptorRecord = PreceptorRecord::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro de preceptor criado com sucesso!',
            'data' => $preceptorRecord
        ]);
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
