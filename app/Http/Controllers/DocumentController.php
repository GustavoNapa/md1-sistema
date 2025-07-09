<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for an inscription
     */
    public function index(Inscription $inscription)
    {
        return view('documents.index', compact('inscription'));
    }

    /**
     * Show the form for creating a new document
     */
    public function create(Inscription $inscription)
    {
        return view('documents.create', compact('inscription'));
    }

    /**
     * Upload document to inscription
     */
    public function upload(Request $request, Inscription $inscription)
    {
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de upload de documentos temporariamente indisponível. O pacote Spatie MediaLibrary precisa ser instalado.'
        ], 501);
    }

    /**
     * Download document
     */
    public function download(Request $request, Inscription $inscription, $mediaId)
    {
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de download temporariamente indisponível.'
        ], 501);
    }

    /**
     * Delete document
     */
    public function destroy(Request $request, Inscription $inscription, $mediaId)
    {
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de exclusão de documentos temporariamente indisponível.'
        ], 501);
    }
}
