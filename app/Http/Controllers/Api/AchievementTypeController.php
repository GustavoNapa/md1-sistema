<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AchievementType;
use Illuminate\Http\Request;

class AchievementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(AchievementType::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
        ]);

        $achievementType = AchievementType::create($validatedData);

        return response()->json($achievementType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AchievementType $achievementType)
    {
        return response()->json($achievementType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AchievementType $achievementType)
    {
        $validatedData = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
        ]);

        $achievementType->update($validatedData);

        return response()->json($achievementType, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AchievementType $achievementType)
    {
        $achievementType->delete();

        return response()->noContent();
    }
}


