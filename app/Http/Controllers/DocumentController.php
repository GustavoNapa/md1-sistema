<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Upload document to inscription
     */
    public function upload(Request $request, Inscription $inscription)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'collection' => 'required|in:documents,contracts,certificates',
            'name' => 'nullable|string|max:255'
        ], [
            'file.required' => 'Selecione um arquivo para upload.',
            'file.max' => 'O arquivo deve ter no máximo 10MB.',
            'collection.required' => 'Tipo de documento é obrigatório.',
            'collection.in' => 'Tipo de documento inválido.'
        ]);

        try {
            $file = $request->file('file');
            $collection = $request->input('collection');
            $customName = $request->input('name') ?: $file->getClientOriginalName();

            // Adicionar arquivo à collection
            $media = $inscription->addMediaFromRequest('file')
                ->usingName($customName)
                ->toMediaCollection($collection);

            return response()->json([
                'success' => true,
                'message' => 'Documento enviado com sucesso!',
                'media' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'url' => $media->getUrl(),
                    'collection' => $media->collection_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete document
     */
    public function destroy(Request $request, Inscription $inscription, $mediaId)
    {
        try {
            $media = $inscription->getMedia()->where('id', $mediaId)->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado.'
                ], 404);
            }

            $media->delete();

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
     * Download document
     */
    public function download(Inscription $inscription, $mediaId)
    {
        $media = $inscription->getMedia()->where('id', $mediaId)->first();

        if (!$media) {
            abort(404, 'Documento não encontrado.');
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    /**
     * Get documents for inscription
     */
    public function index(Inscription $inscription)
    {
        $documents = $inscription->getMedia()->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'size' => $media->size,
                'size_formatted' => $this->formatBytes($media->size),
                'mime_type' => $media->mime_type,
                'collection' => $media->collection_name,
                'created_at' => $media->created_at->format('d/m/Y H:i'),
                'url' => $media->getUrl(),
                'download_url' => route('documents.download', [$inscription, $media->id])
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Show upload form
     */
    public function create(Inscription $inscription)
    {
        return view('documents.upload', compact('inscription'));
    }
}
