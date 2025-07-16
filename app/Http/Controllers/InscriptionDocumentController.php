<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\InscriptionDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InscriptionDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request, Inscription $inscription)
    {
        // Validação básica primeiro
        $basicValidator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:upload,link',
            'category' => 'required|in:contrato,documento_pessoal,certificado,comprovante_pagamento,material_curso,outros',
            'description' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
        ]);

        if ($basicValidator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $basicValidator->errors()
            ], 422);
        }

        // Validação condicional baseada no tipo
        $conditionalRules = [];
        
        if ($request->type === 'upload') {
            $conditionalRules['file'] = 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar|max:10240'; // 10MB
        } elseif ($request->type === 'link') {
            $conditionalRules['external_url'] = 'required|url|max:500';
        }

        if (!empty($conditionalRules)) {
            $conditionalValidator = Validator::make($request->all(), $conditionalRules);
            
            if ($conditionalValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $conditionalValidator->errors()
                ], 422);
            }
        }

        try {
            $data = [
                'inscription_id' => $inscription->id,
                'title' => $request->title,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'is_required' => $request->boolean('is_required'),
            ];

            if ($request->type === 'upload' && $request->hasFile('file')) {
                $file = $request->file('file');
                
                // Gerar nome único para o arquivo
                $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                
                // Salvar arquivo na pasta documents/inscriptions/{id}
                $filePath = $file->storeAs("documents/inscriptions/{$inscription->id}", $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['mime_type'] = $file->getMimeType();
            } elseif ($request->type === 'link') {
                $data['external_url'] = $request->external_url;
            }

            $document = InscriptionDocument::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Documento adicionado com sucesso!',
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'type_label' => $document->type_label,
                    'category_label' => $document->category_label,
                    'status_label' => $document->status_label,
                    'status_badge_class' => $document->status_badge_class,
                    'formatted_file_size' => $document->formatted_file_size,
                    'icon_class' => $document->icon_class,
                    'download_url' => $document->getDownloadUrl(),
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, Inscription $inscription, InscriptionDocument $document)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|in:contrato,documento_pessoal,certificado,comprovante_pagamento,material_curso,outros',
            'description' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
            'external_url' => 'required_if:type,link|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'title' => $request->title,
                'category' => $request->category,
                'description' => $request->description,
                'is_required' => $request->boolean('is_required'),
            ];

            if ($document->type === 'link') {
                $data['external_url'] = $request->external_url;
            }

            $document->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Documento atualizado com sucesso!',
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'type_label' => $document->type_label,
                    'category_label' => $document->category_label,
                    'status_label' => $document->status_label,
                    'status_badge_class' => $document->status_badge_class,
                    'formatted_file_size' => $document->formatted_file_size,
                    'icon_class' => $document->icon_class,
                    'download_url' => $document->getDownloadUrl(),
                    'updated_at' => $document->updated_at->format('d/m/Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Inscription $inscription, InscriptionDocument $document)
    {
        try {
            // Deletar arquivo físico se for upload
            if ($document->type === 'upload' && $document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Documento excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download the specified document.
     */
    public function download(Inscription $inscription, InscriptionDocument $document)
    {
        if ($document->type === 'link') {
            return redirect($document->external_url);
        }

        if ($document->type === 'upload' && $document->file_path) {
            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'Arquivo não encontrado.');
            }

            return Storage::disk('public')->download(
                $document->file_path,
                $document->file_name ?? 'documento.pdf'
            );
        }

        abort(404, 'Documento não encontrado.');
    }

    /**
     * Toggle verification status.
     */
    public function toggleVerification(Request $request, Inscription $inscription, InscriptionDocument $document)
    {
        try {
            $document->update([
                'is_verified' => !$document->is_verified,
                'verified_at' => $document->is_verified ? null : now(),
                'verified_by' => $document->is_verified ? null : auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $document->is_verified ? 'Documento verificado!' : 'Verificação removida!',
                'is_verified' => $document->is_verified,
                'status_label' => $document->status_label,
                'status_badge_class' => $document->status_badge_class,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar verificação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get documents for the inscription.
     */
    public function index(Inscription $inscription)
    {
        $documents = $inscription->documents()
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'documents' => $documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'type' => $document->type,
                    'type_label' => $document->type_label,
                    'category' => $document->category,
                    'category_label' => $document->category_label,
                    'description' => $document->description,
                    'status_label' => $document->status_label,
                    'status_badge_class' => $document->status_badge_class,
                    'formatted_file_size' => $document->formatted_file_size,
                    'icon_class' => $document->icon_class,
                    'download_url' => $document->getDownloadUrl(),
                    'is_required' => $document->is_required,
                    'is_verified' => $document->is_verified,
                    'external_url' => $document->external_url,
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                ];
            })
        ]);
    }
}

