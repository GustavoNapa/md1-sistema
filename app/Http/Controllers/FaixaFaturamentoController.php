<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaixaFaturamentoRequest;
use App\Models\FaixaFaturamento;
use Illuminate\Http\Request;

class FaixaFaturamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FaixaFaturamento::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        $faixas = $query->orderBy('valor_min')->paginate(10);

        return view('faixa-faturamentos.index', compact('faixas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', FaixaFaturamento::class);
        return view('faixa-faturamentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaixaFaturamentoRequest $request)
    {
        FaixaFaturamento::create($request->validated());

        return redirect()->route('faixa-faturamentos.index')
            ->with('success', 'Faixa de faturamento criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(FaixaFaturamento $faixaFaturamento)
    {
        return view('faixa-faturamentos.show', compact('faixaFaturamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FaixaFaturamento $faixaFaturamento)
    {
        $this->authorize('update', $faixaFaturamento);
        return view('faixa-faturamentos.edit', compact('faixaFaturamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaixaFaturamentoRequest $request, FaixaFaturamento $faixaFaturamento)
    {
        $faixaFaturamento->update($request->validated());

        return redirect()->route('faixa-faturamentos.index')
            ->with('success', 'Faixa de faturamento atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FaixaFaturamento $faixaFaturamento)
    {
        $this->authorize('delete', $faixaFaturamento);
        
        $faixaFaturamento->delete();

        return redirect()->route('faixa-faturamentos.index')
            ->with('success', 'Faixa de faturamento excluída com sucesso!');
    }

    /**
     * API endpoint para listar faixas (usado no Kanban)
     */
    public function api()
    {
        $faixas = FaixaFaturamento::orderBy('valor_min')->get();
        
        return response()->json($faixas->map(function ($faixa) {
            return [
                'id' => $faixa->id,
                'label' => $faixa->label,
                'valor_min' => $faixa->valor_min,
                'valor_max' => $faixa->valor_max,
                'range_formatted' => $faixa->range_formatted,
            ];
        }));
    }
}

