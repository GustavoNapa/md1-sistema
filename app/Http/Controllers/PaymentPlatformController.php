<?php
namespace App\Http\Controllers;

use App\Models\PaymentPlatform;
use Illuminate\Http\Request;

class PaymentPlatformController extends Controller
{
    public function index()
    {
        $platforms = PaymentPlatform::all();
        return view('payment_platforms.index', compact('platforms'));
    }

    public function create()
    {
        return view('payment_platforms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:payment_platforms,name',
            'description' => 'nullable|string',
        ]);
        PaymentPlatform::create($request->only(['name', 'description']));
        return redirect()->route('payment_platforms.index')->with('success', 'Plataforma cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $platform = PaymentPlatform::findOrFail($id);
        return view('payment_platforms.edit', compact('platform'));
    }

    public function update(Request $request, $id)
    {
        $platform = PaymentPlatform::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:payment_platforms,name,' . $platform->id,
            'description' => 'nullable|string',
        ]);
        $platform->update($request->only(['name', 'description']));
        return redirect()->route('payment_platforms.index')->with('success', 'Plataforma atualizada com sucesso!');
    }
}
