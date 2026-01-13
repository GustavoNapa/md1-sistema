<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pipelines = Pipeline::withCount(['stages', 'leads'])->get();
        return view('pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        return view('pipelines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:leads,clientes',
            'color' => 'required|string|max:7',
            'is_default' => 'boolean',
            'stages' => 'required|array|min:1',
            'stages.*.name' => 'required|string|max:255',
            'stages.*.order' => 'required|integer',
            'stages.*.color' => 'required|string|max:7',
            'stages.*.type' => 'required|in:normal,ganho,perdido'
        ]);

        $pipeline = Pipeline::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'color' => $validated['color'],
            'is_default' => $validated['is_default'] ?? false
        ]);

        if ($pipeline->is_default) {
            $pipeline->setAsDefault();
        }

        // Criar as etapas
        foreach ($validated['stages'] as $stageData) {
            $pipeline->stages()->create($stageData);
        }

        return redirect()->route('leads.index')
            ->with('success', 'Pipeline criado com sucesso!');
    }

    public function show(Pipeline $pipeline)
    {
        $pipeline->load(['stages', 'leads']);
        return view('pipelines.show', compact('pipeline'));
    }

    public function edit(Pipeline $pipeline)
    {
        $pipeline->load('stages');
        return view('pipelines.edit', compact('pipeline'));
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:leads,clientes',
            'color' => 'required|string|max:7',
            'is_default' => 'boolean',
            'stages' => 'required|array|min:1',
            'stages.*.id' => 'nullable|exists:pipeline_stages,id',
            'stages.*.name' => 'required|string|max:255',
            'stages.*.order' => 'required|integer',
            'stages.*.color' => 'required|string|max:7',
            'stages.*.type' => 'required|in:normal,ganho,perdido'
        ]);

        $pipeline->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'color' => $validated['color'],
            'is_default' => $validated['is_default'] ?? false
        ]);

        if ($pipeline->is_default) {
            $pipeline->setAsDefault();
        }

        // Atualizar etapas existentes e criar novas
        $stageIds = [];
        foreach ($validated['stages'] as $stageData) {
            if (isset($stageData['id'])) {
                $stage = PipelineStage::find($stageData['id']);
                if ($stage && $stage->pipeline_id === $pipeline->id) {
                    $stage->update($stageData);
                    $stageIds[] = $stage->id;
                }
            } else {
                $stage = $pipeline->stages()->create($stageData);
                $stageIds[] = $stage->id;
            }
        }

        // Remover etapas que não estão mais na lista
        $pipeline->stages()->whereNotIn('id', $stageIds)->delete();

        return redirect()->route('pipelines.index')
            ->with('success', 'Pipeline atualizado com sucesso!');
    }

    public function destroy(Pipeline $pipeline)
    {
        $pipeline->delete();
        return redirect()->route('pipelines.index')
            ->with('success', 'Pipeline removido com sucesso!');
    }
}
