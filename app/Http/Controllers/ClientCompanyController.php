<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCompany;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientCompanyController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|size:18|unique:client_companies,cnpj,NULL,id,client_id,' . $request->client_id,
            'type' => 'required|in:clinic,laboratory,hospital,office,other',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|size:9',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'is_main' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);

        $client = Client::findOrFail($request->client_id);

        // Se for marcada como principal, desmarcar outras
        if ($request->boolean('is_main')) {
            $client->companies()->update(['is_main' => false]);
        }

        $company = $client->companies()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa adicionada com sucesso!',
            'data' => $company->load('client')
        ]);
    }

    public function update(Request $request, ClientCompany $clientCompany): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|size:18|unique:client_companies,cnpj,' . $clientCompany->id . ',id,client_id,' . $clientCompany->client_id,
            'type' => 'required|in:clinic,laboratory,hospital,office,other',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|size:9',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'is_main' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Se for marcada como principal, desmarcar outras
        if ($request->boolean('is_main')) {
            $clientCompany->client->companies()
                ->where('id', '!=', $clientCompany->id)
                ->update(['is_main' => false]);
        }

        $clientCompany->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso!',
            'data' => $clientCompany->fresh()
        ]);
    }

    public function destroy(ClientCompany $clientCompany): JsonResponse
    {
        $clientCompany->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa removida com sucesso!'
        ]);
    }

    public function setMain(ClientCompany $clientCompany): JsonResponse
    {
        // Desmarcar outras como principal
        $clientCompany->client->companies()->update(['is_main' => false]);
        
        // Marcar esta como principal
        $clientCompany->update(['is_main' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Empresa definida como principal!',
            'data' => $clientCompany->fresh()
        ]);
    }

    public function show(ClientCompany $clientCompany): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $clientCompany->load('client')
        ]);
    }
}

