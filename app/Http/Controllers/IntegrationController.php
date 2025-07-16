<?php

namespace App\Http\Controllers;

use App\Models\IntegrationSetting;
use App\Models\ZapsignTemplateMapping;
use App\Models\ZapsignDocument;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    /**
     * Display the integrations dashboard.
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'zapsign_templates' => ZapsignTemplateMapping::active()->count(),
            'zapsign_documents_total' => ZapsignDocument::count(),
            'zapsign_documents_signed' => ZapsignDocument::signed()->count(),
            'zapsign_documents_pending' => ZapsignDocument::pending()->count(),
        ];

        // Configurações do ZapSign
        $zapsignSettings = IntegrationSetting::getIntegrationSettings('zapsign');
        $zapsignConfigured = !empty($zapsignSettings['api_token']);

        // Documentos recentes
        $recentDocuments = ZapsignDocument::with(['client', 'inscription', 'templateMapping'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('integrations.index', compact(
            'stats',
            'zapsignConfigured',
            'recentDocuments'
        ));
    }

    /**
     * Display ZapSign integration settings.
     */
    public function zapsign()
    {
        $settings = IntegrationSetting::getIntegrationSettings('zapsign');
        $templateMappings = ZapsignTemplateMapping::active()->orderBy('name')->get();
        
        return view('integrations.zapsign.index', compact('settings', 'templateMappings'));
    }

    /**
     * Update ZapSign settings.
     */
    public function updateZapsignSettings(Request $request)
    {
        $request->validate([
            'api_token' => 'required|string',
            'sandbox_mode' => 'boolean',
            'webhook_url' => 'nullable|url',
        ], [
            'api_token.required' => 'O token da API é obrigatório.',
            'webhook_url.url' => 'A URL do webhook deve ser válida.',
        ]);

        // Salvar configurações
        IntegrationSetting::setValue('zapsign', 'api_token', $request->api_token, true);
        IntegrationSetting::setValue('zapsign', 'sandbox_mode', $request->boolean('sandbox_mode') ? '1' : '0');
        
        if ($request->webhook_url) {
            IntegrationSetting::setValue('zapsign', 'webhook_url', $request->webhook_url);
        }

        return redirect()->route('integrations.zapsign')
            ->with('success', 'Configurações do ZapSign atualizadas com sucesso!');
    }

    /**
     * Test ZapSign connection.
     */
    public function testZapsignConnection()
    {
        try {
            $apiToken = IntegrationSetting::getValue('zapsign', 'api_token');
            
            if (!$apiToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token da API não configurado.'
                ]);
            }

            // Fazer uma requisição simples para testar a conexão
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->get('https://api.zapsign.com.br/api/v1/templates/');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conexão com ZapSign estabelecida com sucesso!',
                    'templates_count' => count($response->json()['results'] ?? [])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro na conexão: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get ZapSign templates from API.
     */
    public function getZapsignTemplates()
    {
        try {
            $apiToken = IntegrationSetting::getValue('zapsign', 'api_token');
            
            if (!$apiToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token da API não configurado.'
                ]);
            }

            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->get('https://api.zapsign.com.br/api/v1/templates/');

            if ($response->successful()) {
                $templates = collect($response->json()['results'] ?? [])
                    ->map(function ($template) {
                        return [
                            'id' => $template['open_id'],
                            'name' => $template['name'],
                            'variables' => $template['variables'] ?? []
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'templates' => $templates
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao buscar templates: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar templates: ' . $e->getMessage()
            ]);
        }
    }
}

