<?php

namespace App\Http\Controllers;

use App\Models\IntegrationSetting;
use App\Models\ZapsignTemplateMapping;
use App\Models\ZapsignDocument;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZapsignController extends Controller
{
    /**
     * Display template mappings.
     */
    public function templateMappings()
    {
        $mappings = ZapsignTemplateMapping::orderBy('name')->paginate(15);
        return view('integrations.zapsign.template-mappings', compact('mappings'));
    }

    /**
     * Show form to create a new template mapping.
     */
    public function createTemplateMapping()
    {
        $systemFields = ZapsignTemplateMapping::getAvailableSystemFields();
        return view('integrations.zapsign.create-mapping', compact('systemFields'));
    }

    /**
     * Get template fields from ZapSign API.
     */
    public function getTemplateFields(Request $request, $templateId)
    {
        try {
            // Buscar configurações do ZapSign
            $apiToken = IntegrationSetting::where('service', 'zapsign')
                ->where('key', 'api_token')
                ->first();
            
            if (!$apiToken || !$apiToken->value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token da API ZapSign não configurado.'
                ], 400);
            }
            
            // Fazer requisição para API ZapSign
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . decrypt($apiToken->value),
                'Accept' => 'application/json',
            ])->get("https://api.zapsign.com.br/api/v1/templates/{$templateId}/");
            
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao buscar template no ZapSign: ' . $response->body()
                ], $response->status());
            }
            
            $templateData = $response->json();
            
            // Extrair campos/inputs do template
            $fields = [];
            if (isset($templateData['inputs']) && is_array($templateData['inputs'])) {
                foreach ($templateData['inputs'] as $input) {
                    $fields[] = [
                        'variable' => $input['variable'] ?? '',
                        'label' => $input['label'] ?? $input['variable'] ?? '',
                        'input_type' => $input['input_type'] ?? 'input',
                        'required' => $input['required'] ?? false,
                        'order' => $input['order'] ?? 0,
                    ];
                }
            }
            
            // Ordenar por ordem se disponível
            usort($fields, function($a, $b) {
                return $a['order'] <=> $b['order'];
            });
            
            return response()->json([
                'success' => true,
                'fields' => $fields,
                'template_name' => $templateData['name'] ?? 'Template',
                'message' => count($fields) . ' campos encontrados.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar campos do template ZapSign: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao buscar campos do template.'
            ], 500);
        }
    }

    /**
     * Store a new template mapping.
     */
    public function storeTemplateMapping(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'zapsign_template_id' => 'required|string',
            'description' => 'nullable|string',
            'auto_sign' => 'boolean',
            'signer_name' => 'required_if:auto_sign,true|nullable|string|max:255',
            'signer_email' => 'required_if:auto_sign,true|nullable|email|max:255',
            'field_mappings' => 'required|array|min:1',
            'field_mappings.*.zapsign_field' => 'required|string',
            'field_mappings.*.system_field' => 'required|string',
            'field_mappings.*.field_type' => 'required|in:text,number,date,email',
            'field_mappings.*.default_value' => 'nullable|string',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'zapsign_template_id.required' => 'O ID do template ZapSign é obrigatório.',
            'signer_name.required_if' => 'Nome do assinante é obrigatório quando assinatura automática está ativa.',
            'signer_email.required_if' => 'E-mail do assinante é obrigatório quando assinatura automática está ativa.',
            'field_mappings.required' => 'Pelo menos um mapeamento de campo é obrigatório.',
            'field_mappings.min' => 'Pelo menos um mapeamento de campo é obrigatório.',
        ]);

        ZapsignTemplateMapping::create([
            'name' => $request->name,
            'zapsign_template_id' => $request->zapsign_template_id,
            'description' => $request->description,
            'field_mappings' => $request->field_mappings,
            'auto_sign' => $request->boolean('auto_sign'),
            'signer_name' => $request->signer_name,
            'signer_email' => $request->signer_email,
            'is_active' => true,
        ]);

        return redirect()->route('integrations.zapsign.template-mappings')
            ->with('success', 'Mapeamento de template criado com sucesso!');
    }

    /**
     * Show form to edit a template mapping.
     */
    public function editTemplateMapping(ZapsignTemplateMapping $mapping)
    {
        $systemFields = ZapsignTemplateMapping::getAvailableSystemFields();
        return view('integrations.zapsign.edit-mapping', compact('mapping', 'systemFields'));
    }

    /**
     * Update a template mapping.
     */
    public function updateTemplateMapping(Request $request, ZapsignTemplateMapping $mapping)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'zapsign_template_id' => 'required|string',
            'description' => 'nullable|string',
            'auto_sign' => 'boolean',
            'signer_name' => 'required_if:auto_sign,true|nullable|string|max:255',
            'signer_email' => 'required_if:auto_sign,true|nullable|email|max:255',
            'field_mappings' => 'required|array|min:1',
            'field_mappings.*.zapsign_field' => 'required|string',
            'field_mappings.*.system_field' => 'required|string',
            'field_mappings.*.field_type' => 'required|in:text,number,date,email',
            'field_mappings.*.default_value' => 'nullable|string',
        ]);

        $mapping->update([
            'name' => $request->name,
            'zapsign_template_id' => $request->zapsign_template_id,
            'description' => $request->description,
            'field_mappings' => $request->field_mappings,
            'auto_sign' => $request->boolean('auto_sign'),
            'signer_name' => $request->signer_name,
            'signer_email' => $request->signer_email,
        ]);

        return redirect()->route('integrations.zapsign.template-mappings')
            ->with('success', 'Mapeamento de template atualizado com sucesso!');
    }

    /**
     * Delete a template mapping.
     */
    public function destroyTemplateMapping(ZapsignTemplateMapping $mapping)
    {
        $mapping->delete();

        return redirect()->route('integrations.zapsign.template-mappings')
            ->with('success', 'Mapeamento de template excluído com sucesso!');
    }

    /**
     * Create document from inscription.
     */
    public function createDocumentFromInscription(Request $request, Inscription $inscription)
    {
        $request->validate([
            'template_mapping_id' => 'required|exists:zapsign_template_mappings,id',
        ]);

        try {
            $mapping = ZapsignTemplateMapping::findOrFail($request->template_mapping_id);
            $apiToken = IntegrationSetting::getValue('zapsign', 'api_token');

            if (!$apiToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token da API ZapSign não configurado.'
                ]);
            }

            // Preparar dados do documento
            $documentData = $this->prepareDocumentData($inscription, $mapping);

            // Criar documento no ZapSign
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->post('https://api.zapsign.com.br/api/v1/docs/', $documentData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Salvar documento no banco
                $document = ZapsignDocument::create([
                    'inscription_id' => $inscription->id,
                    'client_id' => $inscription->client_id,
                    'template_mapping_id' => $mapping->id,
                    'zapsign_document_id' => $responseData['open_id'],
                    'zapsign_token' => $responseData['token'],
                    'external_id' => $responseData['external_id'] ?? null,
                    'name' => $responseData['name'],
                    'status' => $responseData['status'],
                    'original_file_url' => $responseData['original_file'] ?? null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Documento criado com sucesso!',
                    'document' => $document
                ]);
            } else {
                Log::error('Erro ao criar documento ZapSign', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar documento: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar documento ZapSign', [
                'error' => $e->getMessage(),
                'inscription_id' => $inscription->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle ZapSign webhook.
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('ZapSign webhook received', $data);

            if ($data['event_type'] === 'doc_created') {
                $this->handleDocumentCreated($data);
            } elseif ($data['event_type'] === 'doc_signed') {
                $this->handleDocumentSigned($data);
            } elseif ($data['event_type'] === 'doc_expired') {
                $this->handleDocumentExpired($data);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Erro no webhook ZapSign', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Prepare document data for ZapSign API.
     */
    private function prepareDocumentData(Inscription $inscription, ZapsignTemplateMapping $mapping): array
    {
        $inscription->load(['client', 'product', 'vendor']);

        // Preparar variáveis do template
        $variables = [];
        foreach ($mapping->field_mappings as $fieldMapping) {
            $value = $mapping->resolveFieldValue($fieldMapping['system_field'], $inscription);
            if (empty($value) && !empty($fieldMapping['default_value'])) {
                $value = $fieldMapping['default_value'];
            }
            
            $variables[$fieldMapping['zapsign_field']] = $value;
        }

        $documentData = [
            'template_id' => $mapping->zapsign_template_id,
            'external_id' => 'inscription_' . $inscription->id . '_' . time(),
            'name' => $mapping->name . ' - ' . $inscription->client->name,
            'variables' => $variables,
            'signers' => [
                [
                    'name' => $inscription->client->name,
                    'email' => $inscription->client->email,
                    'auth_mode' => 'assinaturaTela',
                ]
            ]
        ];

        // Adicionar assinante automático se configurado
        if ($mapping->auto_sign && $mapping->signer_name && $mapping->signer_email) {
            $documentData['signers'][] = [
                'name' => $mapping->signer_name,
                'email' => $mapping->signer_email,
                'auth_mode' => 'assinaturaTela',
            ];
        }

        return $documentData;
    }

    /**
     * Handle document created webhook.
     */
    private function handleDocumentCreated(array $data): void
    {
        $document = ZapsignDocument::where('zapsign_document_id', $data['open_id'])->first();
        
        if ($document) {
            $document->update([
                'status' => $data['status'],
                'original_file_url' => $data['original_file'] ?? null,
                'webhook_data' => $data,
            ]);
        }
    }

    /**
     * Handle document signed webhook.
     */
    private function handleDocumentSigned(array $data): void
    {
        $document = ZapsignDocument::where('zapsign_document_id', $data['open_id'])->first();
        
        if ($document) {
            $document->update([
                'status' => 'signed',
                'signed_file_url' => $data['signed_file'] ?? null,
                'signed_at' => now(),
                'webhook_data' => $data,
            ]);
        }
    }

    /**
     * Handle document expired webhook.
     */
    private function handleDocumentExpired(array $data): void
    {
        $document = ZapsignDocument::where('zapsign_document_id', $data['open_id'])->first();
        
        if ($document) {
            $document->update([
                'status' => 'expired',
                'expires_at' => now(),
                'webhook_data' => $data,
            ]);
        }
    }
}

