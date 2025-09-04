<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Product;
use App\Events\InscriptionCreated;
use App\Events\InscriptionUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inscription::with(["client", "vendor", "product"]);

        // filtros
        if ($clientName = $request->input('client_name')) {
            $query->whereHas('client', function ($q) use ($clientName) {
                $q->where('name', 'like', "%{$clientName}%");
            });
        }

        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($classGroup = $request->input('class_group')) {
            $query->where('class_group', 'like', "%{$classGroup}%");
        }

        // range de datas sobre start_date (aceita dd/mm/YYYY ou YYYY-mm-dd)
        $from = $request->input('start_date_from');
        $to = $request->input('start_date_to');

        $parseDate = function ($val) {
            if (!$val) return null;
            // formato ISO enviado por date inputs
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                try { return Carbon::createFromFormat('Y-m-d', $val); } catch (\Exception $e) { return null; }
            }
            // formato brasileiro dd/mm/YYYY
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) {
                try { return Carbon::createFromFormat('d/m/Y', $val); } catch (\Exception $e) { return null; }
            }
            // tentativa genérica
            try { return new Carbon($val); } catch (\Exception $e) { return null; }
        };

        $fromDate = $parseDate($from);
        $toDate = $parseDate($to);

        if ($fromDate) {
            $query->whereDate('start_date', '>=', $fromDate->format('Y-m-d'));
        }
        if ($toDate) {
            $query->whereDate('start_date', '<=', $toDate->format('Y-m-d'));
        }

        // ordenação
        $order = $request->input('order_by', 'date_desc');
        switch ($order) {
            case 'date_asc':
                $query->orderBy('start_date', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('start_date', 'desc');
                break;
            case 'value_asc':
                $query->orderBy('valor_total', 'asc');
                break;
            case 'value_desc':
                $query->orderBy('valor_total', 'desc');
                break;
            case 'name_asc':
                $query->join('clients', 'inscriptions.client_id', '=', 'clients.id')
                      ->orderBy('clients.name', 'asc')
                      ->select('inscriptions.*');
                break;
            case 'name_desc':
                $query->join('clients', 'inscriptions.client_id', '=', 'clients.id')
                      ->orderBy('clients.name', 'desc')
                      ->select('inscriptions.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $inscriptions = $query->paginate(15)->appends($request->except('page'));

        // dados para filtros (dropdowns)
        $vendors = Vendor::where('active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $statusOptions = self::getStatusOptions();

    // preparar valores para exibição no formato brasileiro (dd/mm/YYYY)
    $displayStartFrom = $fromDate ? $fromDate->format('d/m/Y') : ($from && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $from) ? $from : null);
    $displayStartTo = $toDate ? $toDate->format('d/m/Y') : ($to && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $to) ? $to : null);

    return view("inscriptions.index", compact("inscriptions", "vendors", "products", "statusOptions", "displayStartFrom", "displayStartTo"));
    }

    /**
     * API endpoint para dados do Kanban
     */
    public function kanbanData(Request $request)
    {
        try {
            $groupBy = $request->get("group_by", "status");
            
            $inscriptions = Inscription::with(["client", "vendor", "product"])
                ->get()
                ->map(function ($inscription) {
                    return [
                        "id" => $inscription->id,
                        "client" => [
                            "name" => $inscription->client->name,
                            "email" => $inscription->client->email ?? "",
                        ],
                        "product" => [
                            "name" => $inscription->product->name ?? "",
                        ],
                        "vendor" => [
                            "name" => $inscription->vendor->name ?? "",
                        ],
                        "status" => $inscription->status,
                        "status_label" => $inscription->status_label,
                        "status_badge_class" => $inscription->status_badge_class,
                        "classification" => $inscription->classification,
                        "calendar_week" => $inscription->calendar_week,
                        "amount_paid" => $inscription->amount_paid,
                        "formatted_amount" => $inscription->formatted_amount,
                        "faixa_faturamento_label" => $inscription->faixa_faturamento_label,
                        "faixa_faturamento_id" => $inscription->getFaixaFaturamento()?->id,
                        "start_date" => $inscription->start_date?->format("d/m/Y"),
                        "class_group" => $inscription->class_group,
                    ];
                });

            // Agrupar por critério selecionado
            $grouped = $this->groupInscriptions($inscriptions, $groupBy);

            return response()->json([
                "inscriptions" => $inscriptions,
                "grouped" => $grouped,
                "group_by" => $groupBy
            ]);
        } catch (\Exception $e) {
            // Logar o erro para depuração
            Log::error("Erro na API do Kanban: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(["error" => "Ocorreu um erro ao carregar os dados do Kanban.", "details" => $e->getMessage()], 500);
        }
    }

    /**
     * Agrupa inscrições por critério
     */
    private function groupInscriptions($inscriptions, $groupBy)
    {
        $grouped = [];

        foreach ($inscriptions as $inscription) {
            $key = $this->getGroupKey($inscription, $groupBy);
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    "label" => $this->getGroupLabel($key, $groupBy),
                    "items" => []
                ];
            }
            
            $grouped[$key]["items"][] = $inscription;
        }

        return $grouped;
    }

    /**
     * Obtém a chave do grupo para uma inscrição
     */
    private function getGroupKey($inscription, $groupBy)
    {
        switch ($groupBy) {
            case "status":
                return $inscription["status"] ?: "sem_status";
            case "faixa_faturamento":
                return $inscription["faixa_faturamento_id"] ?: "sem_faixa";
            case "calendar_week":
                return $inscription["calendar_week"] ?: "sem_semana";
            case "classification":
                return $inscription["classification"] ?: "sem_fase";
            default:
                return "outros";
        }
    }

    /**
     * Obtém o label do grupo
     */
    private function getGroupLabel($key, $groupBy)
    {
        switch ($groupBy) {
            case "status":
                $labels = [
                    "active" => "Ativo",
                    "paused" => "Pausado",
                    "cancelled" => "Cancelado",
                    "completed" => "Concluído",
                    "sem_status" => "Sem Status"
                ];
                return $labels[$key] ?? $key;
            case "faixa_faturamento":
                if ($key === "sem_faixa") {
                    return "Sem Faixa";
                }
                // Buscar o label da faixa no banco
                $faixa = \App\Models\FaixaFaturamento::find($key);
                return $faixa ? $faixa->label : "Faixa {$key}";
            case "calendar_week":
                return $key === "sem_semana" ? "Sem Semana" : "Semana {$key}";
            case "classification":
                return $key === "sem_fase" ? "Sem Fase" : $key;
            default:
                return $key;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where("active", true)->orderBy("name")->get();
        $vendors = Vendor::where("active", true)->orderBy("name")->get();
        $products = Product::where("is_active", true)->orderBy("name")->get();
        $entryChannels = \App\Models\EntryChannel::all();
        $paymentPlatforms = \App\Models\PaymentPlatform::all();
        
        return view("inscriptions.create", compact("clients", "vendors", "products", "entryChannels", "paymentPlatforms"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "client_id" => "required|exists:clients,id",
            "vendor_id" => "nullable|exists:vendors,id",
            "product_id" => "required|exists:products,id",
            "class_group" => "nullable|string|max:255",
            "status" => "required|in:active,paused,cancelled,completed",
            "classification" => "nullable|string|max:255",
            "has_medboss" => "boolean",
            "crmb_number" => "nullable|string|max:255",
            "start_date" => "nullable|date",
            "original_end_date" => "nullable|date|after_or_equal:start_date",
            "actual_end_date" => "nullable|date",
            "calendar_week" => "nullable|integer|min:1|max:52",
            "current_week" => "nullable|integer|min:1|max:52",
            "amount_paid" => "nullable|numeric|min:0",
            "payment_method" => "nullable|string|max:255",
            "commercial_notes" => "nullable|string",
            "general_notes" => "nullable|string",
            "entry_channel" => "nullable|exists:entry_channels,id",
            "contrato_assinado" => "boolean",
            "contrato_na_pasta" => "boolean",
            // Novos campos obrigatórios
            "natureza_juridica" => "required|in:pessoa fisica,pessoa juridica",
            "valor_total" => "required|numeric|min:0",
            "forma_pagamento_entrada" => "required|in:PIX,Boleto,Cartão 1x,Cartão 2x,Cartão 3x,Cartão 4x,Cartão 5x,Cartão 6x,Cartão 7x,Cartão 8x,Cartão 9x,Cartão 10x,Cartão 11x,Cartão 12x,Cartão Recorrencia,Deposito em conta",
            "valor_entrada" => "required|numeric|min:0",
            "data_pagamento_entrada" => "required|date",
            "forma_pagamento_restante" => "required|in:PIX,Boleto,Cartão 1x,Cartão 2x,Cartão 3x,Cartão 4x,Cartão 5x,Cartão 6x,Cartão 7x,Cartão 8x,Cartão 9x,Cartão 10x,Cartão 11x,Cartão 12x,Cartão Recorrencia,Deposito em conta",
            "valor_restante" => "required|numeric|min:0",
            "data_contrato" => "required|date",
            // Campos de endereço
            "cep" => "required|string|max:10",
            "endereco" => "required|string|max:255",
            "numero_casa" => "required|string|max:20",
            "complemento" => "nullable|string|max:255",
            "bairro" => "required|string|max:255",
            "cidade" => "required|string|max:255",
            "estado" => "required|string|size:2",
        ], [
            "client_id.required" => "Selecione um cliente.",
            "client_id.exists" => "Cliente não encontrado.",
            "vendor_id.exists" => "Vendedor não encontrado.",
            "product_id.required" => "Selecione um produto.",
            "product_id.exists" => "Produto não encontrado.",
            "status.required" => "O status é obrigatório.",
            "status.in" => "Status inválido.",
            "original_end_date.after_or_equal" => "A data de término deve ser posterior à data de início.",
            "calendar_week.min" => "Semana deve ser entre 1 e 52.",
            "calendar_week.max" => "Semana deve ser entre 1 e 52.",
            "current_week.min" => "Semana deve ser entre 1 e 52.",
            "current_week.max" => "Semana deve ser entre 1 e 52.",
            "amount_paid.numeric" => "Valor deve ser numérico.",
            "amount_paid.min" => "Valor não pode ser negativo.",
            // Validações dos novos campos
            "natureza_juridica.required" => "A natureza jurídica é obrigatória.",
            "natureza_juridica.in" => "Natureza jurídica inválida.",
            "valor_total.required" => "O valor total é obrigatório.",
            "valor_total.numeric" => "O valor total deve ser numérico.",
            "valor_total.min" => "O valor total não pode ser negativo.",
            "forma_pagamento_entrada.required" => "A forma de pagamento da entrada é obrigatória.",
            "forma_pagamento_entrada.in" => "Forma de pagamento da entrada inválida.",
            "valor_entrada.required" => "O valor da entrada é obrigatório.",
            "valor_entrada.numeric" => "O valor da entrada deve ser numérico.",
            "valor_entrada.min" => "O valor da entrada não pode ser negativo.",
            "data_pagamento_entrada.required" => "A data de pagamento da entrada é obrigatória.",
            "data_pagamento_entrada.date" => "A data de pagamento da entrada deve ser uma data válida.",
            "forma_pagamento_restante.required" => "A forma de pagamento restante é obrigatória.",
            "forma_pagamento_restante.in" => "Forma de pagamento restante inválida.",
            "valor_restante.required" => "O valor restante é obrigatório.",
            "valor_restante.numeric" => "O valor restante deve ser numérico.",
            "valor_restante.min" => "O valor restante não pode ser negativo.",
            "data_contrato.required" => "A data do contrato é obrigatória.",
            "data_contrato.date" => "A data do contrato deve ser uma data válida.",
            // Validações de endereço
            "cep.required" => "O CEP é obrigatório.",
            "endereco.required" => "O endereço é obrigatório.",
            "numero_casa.required" => "O número da casa é obrigatório.",
            "bairro.required" => "O bairro é obrigatório.",
            "cidade.required" => "A cidade é obrigatória.",
            "estado.required" => "O estado é obrigatório.",
            "estado.size" => "O estado deve ter 2 caracteres.",
        ]);

        // Buscar dados do cliente para preencher cpf_cnpj
        $client = Client::find($validated['client_id']);
        $validated['cpf_cnpj'] = $client->cpf;

        $inscription = Inscription::create($validated);

        // Criar endereço
        $client->addresses()->create([
            'cep' => $validated['cep'],
            'endereco' => $validated['endereco'],
            'numero_casa' => $validated['numero_casa'],
            'complemento' => $validated['complemento'],
            'bairro' => $validated['bairro'],
            'cidade' => $validated['cidade'],
            'estado' => $validated['estado'],
        ]);

        // Criar pagamentos
        // Pagamento de Entrada
        $inscription->payments()->create([
            'tipo' => 'Entrada',
            'forma_pagamento' => $validated['forma_pagamento_entrada'],
            'valor' => $validated['valor_entrada'],
            'data_pagamento' => $validated['data_pagamento_entrada'],
            'status' => 'pendente',
        ]);

        // Pagamento Restante
        $inscription->payments()->create([
            'tipo' => 'Pagamento Restante',
            'forma_pagamento' => $validated['forma_pagamento_restante'],
            'valor' => $validated['valor_restante'],
            'data_pagamento' => $validated['data_contrato'],
            'status' => 'pendente',
        ]);

        // Recarregar a inscrição com todos os relacionamentos antes do webhook
        $inscription->load('client.addresses');

        // Disparar evento para webhook
        \App\Events\InscriptionCreated::dispatch($inscription);

        return redirect()->route("inscriptions.show", $inscription)
            ->with("success", "Inscrição criada com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscription $inscription)
    {
        $inscription->load([
            "client", 
            "vendor", 
            "product",
            "preceptorRecords", 
            "payments", 
            "sessions", 
            "diagnostics",
            "onboardingEvents",
            "achievements",
            "followUps",
            "documents"
        ]);
        $achievementTypes = \App\Models\AchievementType::all();
        return view("inscriptions.show", compact("inscription", "achievementTypes"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscription $inscription)
    {
        $clients = Client::where("active", true)->orderBy("name")->get();
        $vendors = Vendor::where("active", true)->orderBy("name")->get();
        $products = Product::where("is_active", true)->orderBy("name")->get();
        $entryChannels = \App\Models\EntryChannel::all();
        
        return view("inscriptions.edit", compact("inscription", "clients", "vendors", "products", "entryChannels"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            "client_id" => "required|exists:clients,id",
            "vendor_id" => "nullable|exists:vendors,id",
            "product_id" => "required|exists:products,id",
            "class_group" => "nullable|string|max:255",
            "status" => "required|in:active,paused,cancelled,completed",
            "classification" => "nullable|string|max:255",
            "has_medboss" => "boolean",
            "crmb_number" => "nullable|string|max:255",
            "start_date" => "nullable|date",
            "original_end_date" => "nullable|date|after_or_equal:start_date",
            "actual_end_date" => "nullable|date",
            "platform_release_date" => "nullable|date",
            "calendar_week" => "nullable|integer|min:1|max:52",
            "current_week" => "nullable|integer|min:1|max:52",
            "amount_paid" => "nullable|numeric|min:0",
            "payment_method" => "nullable|string|max:255",
            "commercial_notes" => "nullable|string",
            "general_notes" => "nullable|string",
            "entry_channel" => "nullable|exists:entry_channels,id",
            "contrato_assinado" => "boolean",
            "contrato_na_pasta" => "boolean",
        ], [
            "client_id.required" => "Selecione um cliente.",
            "client_id.exists" => "Cliente não encontrado.",
            "vendor_id.exists" => "Vendedor não encontrado.",
            "product_id.required" => "Selecione um produto.",
            "product_id.exists" => "Produto não encontrado.",
            "status.required" => "O status é obrigatório.",
            "status.in" => "Status inválido.",
            "original_end_date.after_or_equal" => "A data de término deve ser posterior à data de início.",
            "calendar_week.min" => "Semana deve ser entre 1 e 52.",
            "calendar_week.max" => "Semana deve ser entre 1 e 52.",
            "current_week.min" => "Semana deve ser entre 1 e 52.",
            "current_week.max" => "Semana deve ser entre 1 e 52.",
            "amount_paid.numeric" => "Valor deve ser numérico.",
            "amount_paid.min" => "Valor não pode ser negativo.",
        ]);

        $inscription->update($validated);

        // Disparar evento para webhook
        \App\Events\InscriptionUpdated::dispatch($inscription);

        return redirect()->route("inscriptions.show", $inscription)
            ->with("success", "Inscrição atualizada com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscription $inscription)
    {
        $inscription->delete();

        return redirect()->route("inscriptions.index")
            ->with("success", "Inscrição excluída com sucesso!");
    }

    /**
     * Get status options for forms
     */
    public static function getStatusOptions()
    {
        return [
            "active" => "Ativo",
            "paused" => "Pausado",
            "cancelled" => "Cancelado",
            "completed" => "Concluído"
        ];
    }

    /**
     * Get payment method options
     */
    public static function getPaymentMethodOptions()
    {
        return [
            "credit_card" => "Cartão de Crédito",
            "debit_card" => "Cartão de Débito",
            "bank_transfer" => "Transferência Bancária",
            "pix" => "PIX",
            "boleto" => "Boleto",
            "cash" => "Dinheiro",
            "installments" => "Parcelado"
        ];
    }

    /**
     * Move an inscription card in the Kanban board.
     */
    public function move(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'value' => 'required',
        ]);

        $field = $validated['field'];
        $value = $validated['value'];

        // Validate if the field exists in the Inscription model's fillable properties
        if (!in_array($field, $inscription->getFillable())) {
            return response()->json(['error' => 'Campo inválido para atualização.'], 422);
        }

        // Apply business rules based on the field being updated
        switch ($field) {
            case 'status':
                $currentStatus = $inscription->status;
                // Rules for status transitions
                if ($currentStatus === 'cancelled' && $value !== 'cancelled') {
                    return response()->json(['error' => 'Inscrição cancelada não pode ser reativada.'], 422);
                }
                if ($currentStatus === 'completed' && $value !== 'active') {
                    return response()->json(['error' => 'Inscrição concluída só pode voltar para Ativo.'], 422);
                }
                break;
            case 'calendar_week':
                // Assuming 'calendar_week' is an integer representing the week number
                // You'll need to implement logic to check for future or current weeks
                // and prevent retroceding to closed weeks.
                // For now, a basic check:
                if (!is_numeric($value) || $value < 1 || $value > 52) {
                    return response()->json(['error' => 'Semana inválida.'], 422);
                }
                // Add more complex date/week validation here based on your business logic
                break;
            case 'faixa_faturamento_id':
                // You'll need to implement logic to validate if valor_total fits the target faixa
                // For now, a basic check:
                if (!is_numeric($value)) {
                    return response()->json(['error' => 'Faixa de faturamento inválida.'], 422);
                }
                // Add logic to check if amount_paid fits the new faixa_faturamento_id
                break;
            case 'fase_id':
                // You'll need to implement logic to check if movement is within 1 phase
                // For now, a basic check:
                if (!is_numeric($value)) {
                    return response()->json(['error' => 'Fase inválida.'], 422);
                }
                // Add logic to check phase progression rules
                break;
        }

        $inscription->$field = $value;
        $inscription->save();

        // Dispatch event for real-time updates (if needed)
        // event(new InscriptionMoved($inscription));

        return response()->json(['ok' => true]);
    }
}


