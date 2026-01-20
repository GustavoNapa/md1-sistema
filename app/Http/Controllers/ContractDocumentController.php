<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Inscription $inscription)
    {
        $documents = $inscription->contractDocuments;
        return response()->json($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'file_web_view' => 'nullable|url',
            'token' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max, PDF only
        ], [
            'title.required' => 'O título do documento é obrigatório.',
            'file.required' => 'Por favor, selecione um arquivo PDF.',
            'file.mimes' => 'O arquivo deve ser um PDF.',
            'file.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        $validated['inscription_id'] = $inscription->id;
        $validated['category'] = 'contrato'; // Definir categoria como contrato

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Gerar nome único para o arquivo
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('contract-documents/' . $inscription->id, $fileName, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
            
            // Se nome não foi fornecido, usar o nome original do arquivo
            if (empty($validated['nome'])) {
                $validated['nome'] = $file->getClientOriginalName();
            }
        }

        $document = Document::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Documento adicionado com sucesso!',
            'document' => $document
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscription $inscription, Document $document)
    {
        // Verificar se o documento pertence à inscrição
        if ($document->inscription_id !== $inscription->id) {
            return response()->json([
                'success' => false,
                'message' => 'Documento não encontrado para esta inscrição.'
            ], 404);
        }

        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'file_web_view' => 'nullable|url',
            'token' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'title.required' => 'O título do documento é obrigatório.',
            'file.mimes' => 'O arquivo deve ser um PDF.',
            'file.max' => 'O arquivo não pode ser maior que 10MB.',
        ]);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('contract-documents/' . $inscription->id, $fileName, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
            
            // Atualizar nome se necessário
            if (empty($validated['nome'])) {
                $validated['nome'] = $file->getClientOriginalName();
            }
        }

        $document->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Documento atualizado com sucesso!',
            'document' => $document
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscription $inscription, Document $document)
    {
        // Verificar se o documento pertence à inscrição
        if ($document->inscription_id !== $inscription->id) {
            return response()->json([
                'success' => false,
                'message' => 'Documento não encontrado para esta inscrição.'
            ], 404);
        }

        // Delete file if exists
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento excluído com sucesso!'
        ]);
    }
}
