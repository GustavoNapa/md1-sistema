<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
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
        $products = Product::paginate(15);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0|lt:price',
            'is_active' => 'boolean',
            'webhooks' => 'nullable|array',
            'webhooks.*.webhook_url' => 'nullable|url',
            'webhooks.*.webhook_token' => 'nullable|string',
            'webhooks.*.webhook_trigger_status' => 'nullable|in:active,paused,cancelled,completed',
        ], [
            'name.required' => 'O nome do produto é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço não pode ser negativo.',
            'offer_price.numeric' => 'O preço de oferta deve ser um valor numérico.',
            'offer_price.min' => 'O preço de oferta não pode ser negativo.',
            'offer_price.lt' => 'O preço de oferta deve ser menor que o preço normal.',
            'webhooks.*.webhook_url.url' => 'A URL do webhook deve ser válida.',
            'webhooks.*.webhook_trigger_status.in' => 'Status de gatilho inválido.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product = Product::create($validated);

        // Processar webhooks
        if ($request->has('webhooks')) {
            foreach ($request->webhooks as $webhookData) {
                if (!empty($webhookData['webhook_url'])) {
                    $product->webhooks()->create([
                        'webhook_url' => $webhookData['webhook_url'],
                        'webhook_token' => $webhookData['webhook_token'] ?? null,
                        'webhook_trigger_status' => $webhookData['webhook_trigger_status'] ?? 'active',
                    ]);
                }
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('inscriptions.client');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0|lt:price',
            'is_active' => 'boolean',
            'webhooks' => 'nullable|array',
            'webhooks.*.id' => 'nullable|exists:product_webhooks,id',
            'webhooks.*.webhook_url' => 'nullable|url',
            'webhooks.*.webhook_token' => 'nullable|string',
            'webhooks.*.webhook_trigger_status' => 'nullable|in:active,paused,cancelled,completed',
        ], [
            'name.required' => 'O nome do produto é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço não pode ser negativo.',
            'offer_price.numeric' => 'O preço de oferta deve ser um valor numérico.',
            'offer_price.min' => 'O preço de oferta não pode ser negativo.',
            'offer_price.lt' => 'O preço de oferta deve ser menor que o preço normal.',
            'webhooks.*.webhook_url.url' => 'A URL do webhook deve ser válida.',
            'webhooks.*.webhook_trigger_status.in' => 'Status de gatilho inválido.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        // Processar webhooks
        if ($request->has('webhooks')) {
            $existingWebhookIds = [];
            
            foreach ($request->webhooks as $webhookData) {
                if (!empty($webhookData['webhook_url'])) {
                    if (!empty($webhookData['id'])) {
                        // Atualizar webhook existente
                        $webhook = $product->webhooks()->find($webhookData['id']);
                        if ($webhook) {
                            $webhook->update([
                                'webhook_url' => $webhookData['webhook_url'],
                                'webhook_token' => $webhookData['webhook_token'] ?? null,
                                'webhook_trigger_status' => $webhookData['webhook_trigger_status'] ?? 'active',
                            ]);
                            $existingWebhookIds[] = $webhook->id;
                        }
                    } else {
                        // Criar novo webhook
                        $newWebhook = $product->webhooks()->create([
                            'webhook_url' => $webhookData['webhook_url'],
                            'webhook_token' => $webhookData['webhook_token'] ?? null,
                            'webhook_trigger_status' => $webhookData['webhook_trigger_status'] ?? 'active',
                        ]);
                        $existingWebhookIds[] = $newWebhook->id;
                    }
                }
            }
            
            // Remover webhooks que não estão mais na lista
            $product->webhooks()->whereNotIn('id', $existingWebhookIds)->delete();
        } else {
            // Se não há webhooks no request, remover todos
            $product->webhooks()->delete();
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->inscriptions()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Não é possível excluir um produto que possui inscrições vinculadas.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
