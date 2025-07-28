<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AchievementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $achievementTypes = \App\Models\AchievementType::all();
        return view('achievement_types.index', compact('achievementTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('achievement_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $achievementType = \App\Models\AchievementType::create($validated);
        return redirect()->route('achievement_types.index')->with('success', 'Tipo de conquista criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $achievementType = \App\Models\AchievementType::findOrFail($id);
        return view('achievement_types.show', compact('achievementType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $achievementType = \App\Models\AchievementType::findOrFail($id);
        return view('achievement_types.edit', compact('achievementType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $achievementType = \App\Models\AchievementType::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $achievementType->update($validated);
        return redirect()->route('achievement_types.index')->with('success', 'Tipo de conquista atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $achievementType = \App\Models\AchievementType::findOrFail($id);
        $achievementType->delete();
        return redirect()->route('achievement_types.index')->with('success', 'Tipo de conquista excluído com sucesso!');
    }
}
