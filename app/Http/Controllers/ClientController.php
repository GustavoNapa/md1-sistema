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
        $specialties = \App\Models\Specialty::active()->orderByName()->pluck('name', 'name');
        return view('clients.create', compact('specialties'));
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
            'birth_date' => 'nullable|date|before_or_equal:today',
            'specialty' => 'nullable|exists:specialties,name',
            'service_city' => 'nullable|string|max:255|not_regex:/^[0-9]+$/',
            'state' => 'nullable|string|size:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'region' => 'nullable|string|max:100|not_regex:/^[0-9]+$/|in:Norte,Nordeste,Centro-Oeste,Sudeste,Sul',
            'instagram' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\d\s\(\)\-\+]+$/',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'birth_date.date' => 'Digite uma data válida.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser no futuro.',
            'specialty.exists' => 'Selecione uma especialidade válida.',
            'service_city.not_regex' => 'A cidade não pode conter apenas números.',
            'state.in' => 'Selecione um estado (UF) válido.',
            'state.size' => 'O estado deve ter exatamente 2 caracteres (UF).',
            'region.not_regex' => 'A região não pode conter apenas números.',
            'region.in' => 'Selecione uma região válida.',
            'phone.regex' => 'O telefone deve conter apenas números, espaços, parênteses, hífens e sinal de mais.',
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
        $specialties = \App\Models\Specialty::active()->orderByName()->pluck('name', 'name');
        return view('clients.edit', compact('client', 'specialties'));
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
            'birth_date' => 'nullable|date|before_or_equal:today',
            'specialty' => 'nullable|exists:specialties,name',
            'service_city' => 'nullable|string|max:255|not_regex:/^[0-9]+$/',
            'state' => 'nullable|string|size:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'region' => 'nullable|string|max:100|not_regex:/^[0-9]+$/|in:Norte,Nordeste,Centro-Oeste,Sudeste,Sul',
            'instagram' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[\d\s\(\)\-\+]+$/',
            'active' => 'boolean',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'birth_date.date' => 'Digite uma data válida.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser no futuro.',
            'specialty.exists' => 'Selecione uma especialidade válida.',
            'service_city.not_regex' => 'A cidade não pode conter apenas números.',
            'state.in' => 'Selecione um estado (UF) válido.',
            'state.size' => 'O estado deve ter exatamente 2 caracteres (UF).',
            'region.not_regex' => 'A região não pode conter apenas números.',
            'region.in' => 'Selecione uma região válida.',
            'phone.regex' => 'O telefone deve conter apenas números, espaços, parênteses, hífens e sinal de mais.',
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
