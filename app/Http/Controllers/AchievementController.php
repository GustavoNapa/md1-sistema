<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'achieved_at' => 'nullable|date'
        ]);

        $achievement = Achievement::create($validated);

        // Se for AJAX, retorna JSON. Se for formulário normal, redireciona para inscrição.
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Conquista registrada com sucesso!',
                'data' => $achievement
            ]);
        }

        return redirect()->route('inscriptions.show', $validated['inscription_id'])
            ->with('success', 'Conquista registrada com sucesso!');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conquista excluída com sucesso!'
        ]);
    }
}
