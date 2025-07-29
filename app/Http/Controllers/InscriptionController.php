<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inscriptions = Inscription::with(['client', 'vendor', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('inscriptions.index', compact('inscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('active', true)->orderBy('name')->get();
        $vendors = Vendor::where('active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $entryChannels = \App\Models\EntryChannel::all();
        
        return view('inscriptions.create', compact('clients', 'vendors', 'products', 'entryChannels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_id' => 'required|exists:products,id',
            'class_group' => 'nullable|string|max:255',
            'status' => 'required|in:active,paused,cancelled,completed',
            'classification' => 'nullable|string|max:255',
            'has_medboss' => 'boolean',
            'crmb_number' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'original_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date',
            'platform_release_date' => 'nullable|date',
            'calendar_week' => 'nullable|integer|min:1|max:52',
            'current_week' => 'nullable|integer|min:1|max:52',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'commercial_notes' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'entry_channel' => 'nullable|exists:entry_channels,id',
            'contrato_assinado' => 'boolean',
            'contrato_na_pasta' => 'boolean',
        ], [
            'client_id.required' => 'Selecione um cliente.',
            'client_id.exists' => 'Cliente não encontrado.',
            'vendor_id.exists' => 'Vendedor não encontrado.',
            'product_id.required' => 'Selecione um produto.',
            'product_id.exists' => 'Produto não encontrado.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'Status inválido.',
            'original_end_date.after_or_equal' => 'A data de término deve ser posterior à data de início.',
            'calendar_week.min' => 'Semana deve ser entre 1 e 52.',
            'calendar_week.max' => 'Semana deve ser entre 1 e 52.',
            'current_week.min' => 'Semana deve ser entre 1 e 52.',
            'current_week.max' => 'Semana deve ser entre 1 e 52.',
            'amount_paid.numeric' => 'Valor deve ser numérico.',
            'amount_paid.min' => 'Valor não pode ser negativo.',
        ]);

        $inscription = Inscription::create($validated);

        // Disparar evento para webhook
        \App\Events\InscriptionCreated::dispatch($inscription);

        return redirect()->route('inscriptions.show', $inscription)
            ->with('success', 'Inscrição criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscription $inscription)
    {
        $inscription->load([
            'client', 
            'vendor', 
            'product',
            'preceptorRecords', 
            'payments', 
            'sessions', 
            'diagnostics',
            'onboardingEvents',
            'achievements',
            'followUps',
            'documents'
        ]);
        $achievementTypes = \App\Models\AchievementType::all();
        return view('inscriptions.show', compact('inscription', 'achievementTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscription $inscription)
    {
        $clients = Client::where('active', true)->orderBy('name')->get();
        $vendors = Vendor::where('active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $entryChannels = \App\Models\EntryChannel::all();
        
        return view('inscriptions.edit', compact('inscription', 'clients', 'vendors', 'products', 'entryChannels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_id' => 'required|exists:products,id',
            'class_group' => 'nullable|string|max:255',
            'status' => 'required|in:active,paused,cancelled,completed',
            'classification' => 'nullable|string|max:255',
            'has_medboss' => 'boolean',
            'crmb_number' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'original_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date',
            'platform_release_date' => 'nullable|date',
            'calendar_week' => 'nullable|integer|min:1|max:52',
            'current_week' => 'nullable|integer|min:1|max:52',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'commercial_notes' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'entry_channel' => 'nullable|exists:entry_channels,id',
            'contrato_assinado' => 'boolean',
            'contrato_na_pasta' => 'boolean',
        ], [
            'client_id.required' => 'Selecione um cliente.',
            'client_id.exists' => 'Cliente não encontrado.',
            'vendor_id.exists' => 'Vendedor não encontrado.',
            'product_id.required' => 'Selecione um produto.',
            'product_id.exists' => 'Produto não encontrado.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'Status inválido.',
            'original_end_date.after_or_equal' => 'A data de término deve ser posterior à data de início.',
            'calendar_week.min' => 'Semana deve ser entre 1 e 52.',
            'calendar_week.max' => 'Semana deve ser entre 1 e 52.',
            'current_week.min' => 'Semana deve ser entre 1 e 52.',
            'current_week.max' => 'Semana deve ser entre 1 e 52.',
            'amount_paid.numeric' => 'Valor deve ser numérico.',
            'amount_paid.min' => 'Valor não pode ser negativo.',
        ]);

        $inscription->update($validated);

        // Disparar evento para webhook
        \App\Events\InscriptionUpdated::dispatch($inscription);

        return redirect()->route('inscriptions.show', $inscription)
            ->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscription $inscription)
    {
        $inscription->delete();

        return redirect()->route('inscriptions.index')
            ->with('success', 'Inscrição excluída com sucesso!');
    }

    /**
     * Get status options for forms
     */
    public static function getStatusOptions()
    {
        return [
            'active' => 'Ativo',
            'paused' => 'Pausado',
            'cancelled' => 'Cancelado',
            'completed' => 'Concluído'
        ];
    }

    /**
     * Get payment method options
     */
    public static function getPaymentMethodOptions()
    {
        return [
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'bank_transfer' => 'Transferência Bancária',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            'cash' => 'Dinheiro',
            'installments' => 'Parcelado'
        ];
    }
}
