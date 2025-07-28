<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use Illuminate\Http\Request;
use App\Models\Bonus;

class BonusController extends Controller
{
    public function store(Request $request, Inscription $inscription)
    {
        $validatedData = $request->validate([
            "description" => "required|string|max:255",
            "release_date" => "required|date",
            "expiration_date" => "nullable|date|after_or_equal:release_date",
        ]);

        $bonus = $inscription->bonuses()->create($validatedData);

        // Formatear as datas para o formato correto na resposta
        $bonusData = $bonus->toArray();
        if ($bonus->release_date) {
            $bonusData['release_date'] = $bonus->release_date->format('Y-m-d');
        }
        if ($bonus->expiration_date) {
            $bonusData['expiration_date'] = $bonus->expiration_date->format('Y-m-d');
        }

        return response()->json($bonusData, 201);
    }

    public function show(Inscription $inscription, Bonus $bonus)
    {
        // Verificar se o bônus pertence à inscrição
        if ($bonus->subscription_id !== $inscription->id) {
            return response()->json(['error' => 'Bônus não encontrado'], 404);
        }

        // Formatear as datas para o formato correto
        $bonusData = $bonus->toArray();
        if ($bonus->release_date) {
            $bonusData['release_date'] = $bonus->release_date->format('Y-m-d');
        }
        if ($bonus->expiration_date) {
            $bonusData['expiration_date'] = $bonus->expiration_date->format('Y-m-d');
        }

        return response()->json($bonusData);
    }

    public function update(Request $request, Inscription $inscription, Bonus $bonus)
    {
        // Verificar se o bônus pertence à inscrição
        if ($bonus->subscription_id !== $inscription->id) {
            return response()->json(['error' => 'Bônus não encontrado'], 404);
        }

        $validatedData = $request->validate([
            "description" => "required|string|max:255",
            "release_date" => "required|date",
            "expiration_date" => "nullable|date|after_or_equal:release_date",
        ]);

        $bonus->update($validatedData);

        // Formatear as datas para o formato correto na resposta
        $bonusData = $bonus->toArray();
        if ($bonus->release_date) {
            $bonusData['release_date'] = $bonus->release_date->format('Y-m-d');
        }
        if ($bonus->expiration_date) {
            $bonusData['expiration_date'] = $bonus->expiration_date->format('Y-m-d');
        }

        return response()->json($bonusData);
    }

    public function destroy(Inscription $inscription, Bonus $bonus)
    {
        // Verificar se o bônus pertence à inscrição
        if ($bonus->subscription_id !== $inscription->id) {
            return response()->json(['error' => 'Bônus não encontrado'], 404);
        }

        $bonus->delete();

        return response()->json(['message' => 'Bônus excluído com sucesso']);
    }
}


