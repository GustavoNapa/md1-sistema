<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPhone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientPhoneController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'phone' => 'required|string|min:10|max:15',
            'type' => 'required|in:mobile,landline,work,other',
            'is_whatsapp' => 'boolean',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        $client = Client::findOrFail($request->client_id);

        // Limpar formatação do telefone para validação
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        
        // Verificar se já existe este telefone para o cliente
        $existingPhone = $client->phones()->whereRaw('REGEXP_REPLACE(phone, "[^0-9]", "") = ?', [$cleanPhone])->first();
        if ($existingPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Este telefone já está cadastrado para este cliente.'
            ], 422);
        }

        // Se for marcado como principal, desmarcar outros
        if ($request->boolean('is_primary')) {
            $client->phones()->update(['is_primary' => false]);
        }

        $phone = $client->phones()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Telefone adicionado com sucesso!',
            'data' => $phone->load('client')
        ]);
    }

    public function update(Request $request, ClientPhone $clientPhone): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'type' => 'required|in:mobile,landline,work,other',
            'is_whatsapp' => 'boolean',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        // Limpar formatação do telefone para validação
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        
        // Verificar se já existe este telefone para o cliente (exceto o atual)
        $existingPhone = $clientPhone->client->phones()
            ->where('id', '!=', $clientPhone->id)
            ->whereRaw('REGEXP_REPLACE(phone, "[^0-9]", "") = ?', [$cleanPhone])
            ->first();
            
        if ($existingPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Este telefone já está cadastrado para este cliente.'
            ], 422);
        }

        // Se for marcado como principal, desmarcar outros
        if ($request->boolean('is_primary')) {
            $clientPhone->client->phones()
                ->where('id', '!=', $clientPhone->id)
                ->update(['is_primary' => false]);
        }

        $clientPhone->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Telefone atualizado com sucesso!',
            'data' => $clientPhone->fresh()
        ]);
    }

    public function destroy(ClientPhone $clientPhone): JsonResponse
    {
        // Não permitir excluir se for o único telefone ou se for principal
        if ($clientPhone->is_primary && $clientPhone->client->phones()->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir o telefone principal. Defina outro como principal primeiro.'
            ], 422);
        }

        $clientPhone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Telefone removido com sucesso!'
        ]);
    }

    public function setPrimary(ClientPhone $clientPhone): JsonResponse
    {
        // Desmarcar outros como principal
        $clientPhone->client->phones()->update(['is_primary' => false]);
        
        // Marcar este como principal
        $clientPhone->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Telefone definido como principal!',
            'data' => $clientPhone->fresh()
        ]);
    }
}

