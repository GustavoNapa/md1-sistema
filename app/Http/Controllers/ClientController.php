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
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients',
            'email' => 'required|email|unique:clients',
            'birth_date' => 'nullable|date',
            'specialty' => 'nullable|string|max:255',
            'service_city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'birth_date.date' => 'Digite uma data válida.',
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
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients,cpf,' . $client->id,
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'birth_date' => 'nullable|date',
            'specialty' => 'nullable|string|max:255',
            'service_city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'active' => 'boolean',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'birth_date.date' => 'Digite uma data válida.',
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
