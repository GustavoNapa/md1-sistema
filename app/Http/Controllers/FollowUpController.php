<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $followUp = FollowUp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up registrado com sucesso!',
            'data' => $followUp
        ]);
    }

    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Follow-up excluído com sucesso!'
        ]);
    }
}
