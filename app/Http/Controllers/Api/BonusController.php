<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Bonus;

class BonusController extends Controller
{
    public function store(Request $request, Subscription $subscription)
    {
        $validatedData = $request->validate([
            "description" => "required|string|max:255",
            "release_date" => "required|date",
            "expiration_date" => "nullable|date|after_or_equal:release_date",
        ]);

        $bonus = $subscription->bonuses()->create($validatedData);

        return response()->json($bonus, 201);
    }
}


