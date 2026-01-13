<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $pipelines = Pipeline::where('type', 'leads')
            ->withCount('leads')
            ->with(['stages' => function($query) {
                $query->withCount('leads');
            }])
            ->get();

        // Pipeline selecionado (o primeiro ou o especificado na query string)
        $selectedPipelineId = $request->get('pipeline_id');
        
        if ($selectedPipelineId) {
            $selectedPipeline = Pipeline::with(['stages.leads.user'])->find($selectedPipelineId);
        } else {
            $selectedPipeline = $pipelines->first();
        }

        // Contar leads sem pipeline e arquivados
        $withoutPipelineCount = Lead::withoutPipeline()->count();
        $archivedCount = Lead::archived()->count();

        return view('leads.index', compact('pipelines', 'selectedPipeline', 'withoutPipelineCount', 'archivedCount'));
    }

    public function withoutPipeline()
    {
        $leads = Lead::withoutPipeline()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $pipelines = Pipeline::where('type', 'leads')->get();
        
        return view('leads.without-pipeline', compact('leads', 'pipelines'));
    }

    public function archived()
    {
        $leads = Lead::archived()
            ->with(['user', 'pipeline', 'stage'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('leads.archived', compact('leads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_whatsapp' => 'boolean',
            'email' => 'nullable|email|max:255',
            'origin' => 'required|string',
            'origin_other' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'pipeline_stage_id' => 'nullable|exists:pipeline_stages,id',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $validated['is_whatsapp'] = $request->has('is_whatsapp');

        // Se há pipeline_id mas está vazio, remove
        if (isset($validated['pipeline_id']) && empty($validated['pipeline_id'])) {
            $validated['pipeline_id'] = null;
            $validated['pipeline_stage_id'] = null;
        }

        // Se há pipeline_id definido
        if (!empty($validated['pipeline_id'])) {
            // Se não foi especificada uma etapa, usa a primeira do pipeline
            if (empty($validated['pipeline_stage_id'])) {
                $pipeline = Pipeline::findOrFail($validated['pipeline_id']);
                $firstStage = $pipeline->stages()->orderBy('order')->first();
                
                if (!$firstStage) {
                    return back()->withErrors(['pipeline_stage_id' => 'O pipeline não possui etapas configuradas.']);
                }
                
                $validated['pipeline_stage_id'] = $firstStage->id;
            }

            // Define a ordem na etapa
            $maxOrder = Lead::where('pipeline_stage_id', $validated['pipeline_stage_id'])->max('stage_order') ?? 0;
            $validated['stage_order'] = $maxOrder + 1;
        } else {
            // Lead sem pipeline
            $validated['pipeline_id'] = null;
            $validated['pipeline_stage_id'] = null;
            $validated['stage_order'] = 0;
        }

        // Se não foi especificado responsável, usa o usuário autenticado
        if (empty($validated['user_id'])) {
            $validated['user_id'] = Auth::id();
        }

        Lead::create($validated);

        // Redireciona para a página apropriada
        if (!empty($validated['pipeline_id'])) {
            return redirect()->route('leads.index', ['pipeline_id' => $validated['pipeline_id']])
                ->with('success', 'Lead criado com sucesso!');
        } else {
            return redirect()->route('leads.without-pipeline')
                ->with('success', 'Lead criado com sucesso!');
        }
    }

    public function show(Lead $lead)
    {
        $lead->load(['pipeline', 'stage', 'user']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $pipelines = Pipeline::where('type', 'leads')->get();
        $users = User::orderBy('name')->get();
        return view('leads.edit', compact('lead', 'pipelines', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_whatsapp' => 'boolean',
            'email' => 'nullable|email|max:255',
            'origin' => 'required|string',
            'origin_other' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $validated['is_whatsapp'] = $request->has('is_whatsapp');

        $lead->update($validated);

        return redirect()->route('leads.index', ['pipeline_id' => $lead->pipeline_id])
            ->with('success', 'Lead atualizado com sucesso!');
    }

    public function destroy(Lead $lead)
    {
        $pipelineId = $lead->pipeline_id;
        $lead->delete();

        return redirect()->route('leads.index', ['pipeline_id' => $pipelineId])
            ->with('success', 'Lead removido com sucesso!');
    }

    public function moveStage(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:pipeline_stages,id',
            'order' => 'nullable|integer'
        ]);

        try {
            $lead->moveToStage($validated['stage_id']);
            
            if (isset($validated['order'])) {
                $lead->update(['stage_order' => $validated['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead movido com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function archive(Lead $lead)
    {
        $lead->archive();
        
        return redirect()->back()->with('success', 'Lead arquivado com sucesso!');
    }

    public function restoreLead(Lead $lead)
    {
        $lead->restore();
        
        return redirect()->back()->with('success', 'Lead restaurado com sucesso!');
    }

    public function assignPipeline(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'pipeline_id' => 'required|exists:pipelines,id'
        ]);

        try {
            $lead->assignToPipeline($validated['pipeline_id']);
            
            return redirect()->route('leads.index', ['pipeline_id' => $validated['pipeline_id']])
                ->with('success', 'Pipeline atribuído com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['pipeline_id' => $e->getMessage()]);
        }
    }

    public function changePipeline(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'pipeline_id' => 'required|exists:pipelines,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id'
        ]);

        try {
            // Verificar se a etapa pertence ao pipeline
            $stage = \App\Models\PipelineStage::findOrFail($validated['pipeline_stage_id']);
            if ($stage->pipeline_id != $validated['pipeline_id']) {
                throw new \Exception('A etapa selecionada não pertence ao pipeline escolhido.');
            }

            // Atualizar o lead
            $lead->update([
                'pipeline_id' => $validated['pipeline_id'],
                'pipeline_stage_id' => $validated['pipeline_stage_id']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pipeline e etapa alterados com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
