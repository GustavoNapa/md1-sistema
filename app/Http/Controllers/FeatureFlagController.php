<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Illuminate\Support\Facades\DB;

class FeatureFlagController extends Controller
{
    /**
     * Lista de feature flags disponíveis no sistema
     */
    private $availableFeatures = [
        'webhook_system' => [
            'name' => 'Sistema de Webhooks',
            'description' => 'Habilita o sistema de webhooks para inscrições',
            'category' => 'Integração'
        ],
        'kanban_view' => [
            'name' => 'Visualização Kanban',
            'description' => 'Habilita a visualização em Kanban das inscrições',
            'category' => 'Interface'
        ],
        'whatsapp_integration' => [
            'name' => 'Integração WhatsApp',
            'description' => 'Habilita a integração com WhatsApp via Evolution API',
            'category' => 'Integração'
        ],
        'advanced_filters' => [
            'name' => 'Filtros Avançados',
            'description' => 'Habilita filtros avançados nas listagens',
            'category' => 'Interface'
        ],
        'export_features' => [
            'name' => 'Funcionalidades de Exportação',
            'description' => 'Habilita exportação de dados em Excel/PDF',
            'category' => 'Relatórios'
        ],
        'notification_system' => [
            'name' => 'Sistema de Notificações',
            'description' => 'Habilita notificações em tempo real',
            'category' => 'Comunicação'
        ],
        'audit_logs' => [
            'name' => 'Logs de Auditoria',
            'description' => 'Habilita logs detalhados de ações do sistema',
            'category' => 'Segurança'
        ],
        'api_access' => [
            'name' => 'Acesso à API',
            'description' => 'Habilita acesso às APIs do sistema',
            'category' => 'Integração'
        ]
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = [];
        
        foreach ($this->availableFeatures as $key => $config) {
            $features[] = [
                'key' => $key,
                'name' => $config['name'],
                'description' => $config['description'],
                'category' => $config['category'],
                'enabled' => $this->isFeatureEnabled($key),
                'scope_count' => $this->getFeatureScopeCount($key)
            ];
        }

        // Agrupar por categoria
        $groupedFeatures = collect($features)->groupBy('category');

        return view('feature-flags.index', compact('groupedFeatures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('feature-flags.create', [
            'availableFeatures' => $this->availableFeatures
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'feature_key' => 'required|string',
            'enabled' => 'required|boolean',
            'scope_type' => 'nullable|string|in:global,user,role',
            'scope_id' => 'nullable|integer'
        ]);

        $featureKey = $request->feature_key;
        $enabled = $request->boolean('enabled');
        $scopeType = $request->scope_type ?? 'global';
        $scopeId = $request->scope_id;

        if ($scopeType === 'global') {
            // Feature flag global
            if ($enabled) {
                Feature::activate($featureKey);
            } else {
                Feature::deactivate($featureKey);
            }
        } else {
            // Feature flag com escopo específico
            $scope = $this->resolveScope($scopeType, $scopeId);
            if ($scope) {
                if ($enabled) {
                    Feature::for($scope)->activate($featureKey);
                } else {
                    Feature::for($scope)->deactivate($featureKey);
                }
            }
        }

        return redirect()->route('feature-flags.index')
            ->with('success', 'Feature flag atualizada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $featureKey)
    {
        if (!isset($this->availableFeatures[$featureKey])) {
            abort(404, 'Feature flag não encontrada');
        }

        $feature = $this->availableFeatures[$featureKey];
        $feature['key'] = $featureKey;
        $feature['enabled'] = $this->isFeatureEnabled($featureKey);
        
        // Buscar todos os escopos onde esta feature está ativa
        $activeScopes = DB::table('features')
            ->where('name', $featureKey)
            ->where('value', 'true')
            ->get();

        return view('feature-flags.show', compact('feature', 'activeScopes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $featureKey)
    {
        if (!isset($this->availableFeatures[$featureKey])) {
            abort(404, 'Feature flag não encontrada');
        }

        $feature = $this->availableFeatures[$featureKey];
        $feature['key'] = $featureKey;
        $feature['enabled'] = $this->isFeatureEnabled($featureKey);

        return view('feature-flags.edit', compact('feature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $featureKey)
    {
        $request->validate([
            'enabled' => 'required|boolean'
        ]);

        $enabled = $request->boolean('enabled');

        if ($enabled) {
            Feature::activate($featureKey);
        } else {
            Feature::deactivate($featureKey);
        }

        return redirect()->route('feature-flags.index')
            ->with('success', 'Feature flag atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $featureKey)
    {
        // Remove todas as entradas desta feature flag
        DB::table('features')->where('name', $featureKey)->delete();

        return redirect()->route('feature-flags.index')
            ->with('success', 'Feature flag removida com sucesso!');
    }

    /**
     * Toggle feature flag status
     */
    public function toggle(Request $request, string $featureKey)
    {
        if (!isset($this->availableFeatures[$featureKey])) {
            return response()->json(['error' => 'Feature flag não encontrada'], 404);
        }

        $currentStatus = $this->isFeatureEnabled($featureKey);
        
        if ($currentStatus) {
            Feature::deactivate($featureKey);
        } else {
            Feature::activate($featureKey);
        }

        return response()->json([
            'success' => true,
            'enabled' => !$currentStatus,
            'message' => 'Feature flag atualizada com sucesso!'
        ]);
    }

    /**
     * Verifica se uma feature está habilitada globalmente
     */
    private function isFeatureEnabled(string $featureKey): bool
    {
        return Feature::active($featureKey);
    }

    /**
     * Conta quantos escopos têm esta feature ativa
     */
    private function getFeatureScopeCount(string $featureKey): int
    {
        return DB::table('features')
            ->where('name', $featureKey)
            ->where('value', 'true')
            ->count();
    }

    /**
     * Resolve o escopo baseado no tipo e ID
     */
    private function resolveScope(string $scopeType, ?int $scopeId)
    {
        switch ($scopeType) {
            case 'user':
                return $scopeId ? \App\Models\User::find($scopeId) : null;
            case 'role':
                // Implementar quando houver sistema de roles
                return null;
            default:
                return null;
        }
    }
}

