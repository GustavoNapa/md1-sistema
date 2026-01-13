<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Specialty;
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
    public function index(Request $request)
    {
        $q = $request->input('q');

    $clientsQuery = Client::with('inscriptions')->withCount('inscriptions');

        if ($q) {
            $normalized = preg_replace('/\D/', '', $q);

            $clientsQuery->where(function ($query) use ($q, $normalized) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");

                if ($normalized !== '') {
                    // compara o CPF sem formatação
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%{$normalized}%"]);
                    // compara o telefone sem formatação: remove parênteses, traços, espaços e pontos
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '(', ''), ')', ''), '-', ''), ' ', ''), '.', '') LIKE ?", ["%{$normalized}%"]);
                }
            });
        }

        // filtro por status (ativo/inativo)
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $clientsQuery->where('active', true);
            } elseif ($status === 'inactive') {
                $clientsQuery->where('active', false);
            }
        }

        // filtro por especialidade
        if ($specialty = $request->input('specialty')) {
            $clientsQuery->where('specialty', $specialty);
        }

        // ordenação
        $order = $request->input('order_by', 'name_asc');
        switch ($order) {
            case 'name_desc':
                $clientsQuery->orderBy('name', 'desc');
                break;
            case 'inscriptions_asc':
                $clientsQuery->orderBy('inscriptions_count', 'asc');
                break;
            case 'inscriptions_desc':
                $clientsQuery->orderBy('inscriptions_count', 'desc');
                break;
            case 'name_asc':
            default:
                $clientsQuery->orderBy('name', 'asc');
                break;
        }

        $clients = $clientsQuery->paginate(15)->appends($request->except('page'));

        $specialties = Specialty::active()->orderByName()->pluck('name', 'name');

        return view('clients.index', compact('clients', 'specialties'));
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
        // Normaliza media_faturamento antes da validação para evitar inserir formato BR no DB
        if ($request->has('media_faturamento')) {
            $request->merge([
                'media_faturamento' => $this->normalizeBrazilianDecimal($request->input('media_faturamento'))
            ]);
        }

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
            'phone' => 'nullable|string|max:20|regex:/^(\+?55\s?)?\(?(\d{2})\)?\s?(\d{4,5})\-?(\d{4})$/',
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
            'phone.regex' => 'O telefone deve estar no formato brasileiro válido. Ex: (11) 98765-4321 ou 11987654321.',
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
        $client->load([
            'inscriptions.vendor', 
            'inscriptions.product',
            'inscriptions.preceptorRecords',
            'inscriptions.payments',
            'inscriptions.sessions',
            'inscriptions.diagnostics',
            'inscriptions.achievements',
            'inscriptions.followUps',
            'whatsappMessages'
        ]);
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
        // Normaliza media_faturamento antes da validação para evitar inserir formato BR no DB
        if ($request->has('media_faturamento')) {
            $request->merge([
                'media_faturamento' => $this->normalizeBrazilianDecimal($request->input('media_faturamento'))
            ]);
        }
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
            'phone' => 'nullable|string|max:20|regex:/^(\+?55\s?)?\(?(\d{2})\)?\s?(\d{4,5})\-?(\d{4})$/',
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
            'phone.regex' => 'O telefone deve estar no formato brasileiro válido. Ex: (11) 98765-4321 ou 11987654321.',
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

    /**
     * Display kanban view for clients.
     */
    public function kanban(Request $request)
    {
        $q = $request->input('q');

        $clientsQuery = Client::with(['inscriptions' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->withCount('inscriptions');

        if ($q) {
            $normalized = preg_replace('/\D/', '', $q);

            $clientsQuery->where(function ($query) use ($q, $normalized) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");

                if ($normalized !== '') {
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') LIKE ?", ["%{$normalized}%"]);
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '(', ''), ')', ''), '-', ''), ' ', ''), '.', '') LIKE ?", ["%{$normalized}%"]);
                }
            });
        }

        // filtro por status (ativo/inativo)
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $clientsQuery->where('active', true);
            } elseif ($status === 'inactive') {
                $clientsQuery->where('active', false);
            }
        }

        // filtro por especialidade
        if ($specialty = $request->input('specialty')) {
            $clientsQuery->where('specialty', $specialty);
        }

        $clients = $clientsQuery->orderBy('name', 'asc')->get();

        // Agrupar clientes por status das inscrições
        $leadClients = [];
        $activeClients = [];
        $inactiveClients = [];
        $completedClients = [];

        foreach ($clients as $client) {
            if ($client->inscriptions->isEmpty()) {
                $leadClients[] = $client;
            } else {
                $hasActive = false;
                $hasCompleted = false;
                
                foreach ($client->inscriptions as $inscription) {
                    if (in_array($inscription->status, ['active', 'pending'])) {
                        $hasActive = true;
                    }
                    if (in_array($inscription->status, ['completed', 'cancelled'])) {
                        $hasCompleted = true;
                    }
                }

                if ($hasActive) {
                    $activeClients[] = $client;
                } elseif ($hasCompleted) {
                    $completedClients[] = $client;
                } elseif (!$client->active) {
                    $inactiveClients[] = $client;
                }
            }
        }

        $specialties = \App\Models\Specialty::active()->orderByName()->pluck('name', 'name');

        return view('clients.kanban', compact('leadClients', 'activeClients', 'inactiveClients', 'completedClients', 'specialties'));
    }

    /**
     * Update client status based on kanban column.
     */
    public function updateKanbanStatus(Request $request, Client $client)
    {
        $request->validate([
            'column' => 'required|in:lead,active,completed,inactive'
        ]);

        $column = $request->input('column');

        // Atualizar status do cliente baseado na coluna
        switch ($column) {
            case 'lead':
                // Cliente volta a ser lead - não precisa fazer nada específico
                // mas garantimos que está ativo
                $client->active = true;
                $client->save();
                break;

            case 'active':
                // Cliente ativo - garantir que está ativo
                $client->active = true;
                $client->save();
                break;

            case 'completed':
                // Cliente concluído - manter ativo mas indicar conclusão
                $client->active = true;
                $client->save();
                break;

            case 'inactive':
                // Cliente inativo
                $client->active = false;
                $client->save();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Cliente movido com sucesso!',
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'active' => $client->active
            ]
        ]);
    }

    /**
     * Normaliza um número decimal no formato brasileiro para o formato padrão.
     */
    private function normalizeBrazilianDecimal($value)
    {
        // Remove todos os caracteres que não são dígitos ou vírgulas
        $value = preg_replace('/[^\d,]/', '', $value);

        // Substitui a vírgula por um ponto
        $value = str_replace(',', '.', $value);

        return $value;
    }
}
