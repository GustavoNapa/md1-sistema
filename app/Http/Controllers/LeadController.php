<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadHistory;
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

        $lead = Lead::create($validated);

        // Registrar no histórico
        LeadHistory::logAction(
            $lead->id,
            'created',
            'Lead criado no sistema',
            [
                'pipeline' => $lead->pipeline?->name,
                'stage' => $lead->stage?->name,
                'assigned_to' => $lead->user?->name
            ]
        );

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
        $lead->load(['pipeline', 'stage', 'user', 'histories.user', 'customFieldValues.customField.fieldGroup']);
        
        // Carregar grupos de campos com seus campos e valores
        $fieldGroups = \App\Models\FieldGroup::with(['customFields'])
            ->where('type', 'contato')
            ->orderBy('order')
            ->get()
            ->map(function ($group) use ($lead) {
                $group->customFields->each(function ($field) use ($lead) {
                    $value = $lead->customFieldValues->where('custom_field_id', $field->id)->first();
                    $field->value = $value ? $value->value : null;
                });
                return $group;
            });
        
        return view('leads.show', compact('lead', 'fieldGroups'));
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

        // Capturar alterações
        $changes = [];
        $changed = false;
        
        if ($lead->name !== $validated['name']) {
            $changes['name'] = ['old' => $lead->name, 'new' => $validated['name']];
            $changed = true;
        }
        if ($lead->phone !== $validated['phone']) {
            $changes['phone'] = ['old' => $lead->phone, 'new' => $validated['phone']];
            $changed = true;
        }
        if ($lead->email !== $validated['email']) {
            $changes['email'] = ['old' => $lead->email, 'new' => $validated['email']];
            $changed = true;
        }
        if ($lead->user_id !== $validated['user_id']) {
            $oldUser = $lead->user?->name ?? 'Não atribuído';
            $newUser = User::find($validated['user_id'])?->name ?? 'Não atribuído';
            $changes['assigned_to'] = ['old' => $oldUser, 'new' => $newUser];
            $changed = true;
        }

        $lead->update($validated);

        // Registrar no histórico se houver mudanças
        if ($changed) {
            LeadHistory::logAction(
                $lead->id,
                'updated',
                'Informações do lead foram atualizadas',
                $changes
            );
        }

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
            $oldStage = $lead->stage;
            $lead->moveToStage($validated['stage_id']);
            
            // Registrar no histórico
            $newStage = PipelineStage::find($validated['stage_id']);
            LeadHistory::logAction(
                $lead->id,
                'stage_changed',
                "Etapa alterada de \"{$oldStage->name}\" para \"{$newStage->name}\"",
                [
                    'old_stage' => $oldStage->name,
                    'new_stage' => $newStage->name
                ]
            );
            
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
        
        // Registrar no histórico
        LeadHistory::logAction(
            $lead->id,
            'archived',
            'Lead foi arquivado'
        );
        
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

            $oldPipeline = $lead->pipeline;
            $oldStage = $lead->stage;
            
            // Atualizar o lead
            $lead->update([
                'pipeline_id' => $validated['pipeline_id'],
                'pipeline_stage_id' => $validated['pipeline_stage_id']
            ]);
            
            // Registrar no histórico
            $newPipeline = Pipeline::find($validated['pipeline_id']);
            $newStage = PipelineStage::find($validated['pipeline_stage_id']);
            
            LeadHistory::logAction(
                $lead->id,
                'pipeline_changed',
                "Pipeline alterado de \"{$oldPipeline->name}\" para \"{$newPipeline->name}\"",
                [
                    'old_pipeline' => $oldPipeline->name,
                    'new_pipeline' => $newPipeline->name,
                    'old_stage' => $oldStage->name,
                    'new_stage' => $newStage->name
                ]
            );
            
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
