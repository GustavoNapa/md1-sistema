<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'diagnosis' => 'required|string|max:255',
            'date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $diagnostic = Diagnostic::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Diagnóstico registrado com sucesso!',
            'data' => $diagnostic
        ]);
    }

    public function destroy(Diagnostic $diagnostic)
    {
        $diagnostic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diagnóstico excluído com sucesso!'
        ]);
    }
}
