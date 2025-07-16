<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientEmailController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'email' => 'required|email|unique:client_emails,email,NULL,id,client_id,' . $request->client_id,
            'type' => 'required|in:personal,work,other',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        $client = Client::findOrFail($request->client_id);

        // Se for marcado como principal, desmarcar outros
        if ($request->boolean('is_primary')) {
            $client->emails()->update(['is_primary' => false]);
        }

        $email = $client->emails()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'E-mail adicionado com sucesso!',
            'data' => $email->load('client')
        ]);
    }

    public function update(Request $request, ClientEmail $clientEmail): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:client_emails,email,' . $clientEmail->id . ',id,client_id,' . $clientEmail->client_id,
            'type' => 'required|in:personal,work,other',
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        // Se for marcado como principal, desmarcar outros
        if ($request->boolean('is_primary')) {
            $clientEmail->client->emails()
                ->where('id', '!=', $clientEmail->id)
                ->update(['is_primary' => false]);
        }

        $clientEmail->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'E-mail atualizado com sucesso!',
            'data' => $clientEmail->fresh()
        ]);
    }

    public function destroy(ClientEmail $clientEmail): JsonResponse
    {
        // Não permitir excluir se for o único e-mail ou se for principal
        if ($clientEmail->is_primary && $clientEmail->client->emails()->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir o e-mail principal. Defina outro como principal primeiro.'
            ], 422);
        }

        $clientEmail->delete();

        return response()->json([
            'success' => true,
            'message' => 'E-mail removido com sucesso!'
        ]);
    }

    public function setPrimary(ClientEmail $clientEmail): JsonResponse
    {
        // Desmarcar outros como principal
        $clientEmail->client->emails()->update(['is_primary' => false]);
        
        // Marcar este como principal
        $clientEmail->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'E-mail definido como principal!',
            'data' => $clientEmail->fresh()
        ]);
    }

    public function verify(ClientEmail $clientEmail): JsonResponse
    {
        $clientEmail->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'E-mail verificado com sucesso!',
            'data' => $clientEmail->fresh()
        ]);
    }
}

