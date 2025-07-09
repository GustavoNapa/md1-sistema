<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with('inscriptions')->paginate(15);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients',
            'email' => 'required|email|unique:clients',
            'data_nascimento' => 'nullable|date',
            'especialidade' => 'nullable|string|max:255',
            'cidade_atendimento' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
            'regiao' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['inscriptions.vendor', 'whatsappMessages']);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients,cpf,' . $client->id,
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'data_nascimento' => 'nullable|date',
            'especialidade' => 'nullable|string|max:255',
            'cidade_atendimento' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
            'regiao' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'ativo' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}
