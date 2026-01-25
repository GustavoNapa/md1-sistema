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

        // ordenação: padrão agora é por created_at (mais recentes)
        $order = $request->input('order_by', 'created_at_desc');
        switch ($order) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
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
                // fallback para created_at desc
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
        $paymentChannels = \App\Models\PaymentChannel::where('active', true)->orderBy('name')->get();
        
        return view("inscriptions.create", compact("clients", "vendors", "products", "entryChannels", "paymentPlatforms", "paymentChannels"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log incoming request for debugging
        Log::info('InscriptionController@store called', ['input' => $request->all()]);

        try {
            // dynamic validation: support 'parcelado' checkbox vs single avista payment
            $paymentPlatformNames = \App\Models\PaymentPlatform::pluck('name')->toArray();
            $platformsRule = count($paymentPlatformNames) ? 'in:' . implode(',', $paymentPlatformNames) : 'string';

            // Normalize incoming payment platform inputs (accept case-insensitive values e.g. 'pix' or 'PIX')
            $normalizePlatform = function ($value) use ($paymentPlatformNames) {
                if ($value === null || $value === '') return $value;
                foreach ($paymentPlatformNames as $p) {
                    if (strcasecmp($p, $value) === 0) return $p;
                }
                return $value;
            };

            $toNormalize = ['meio_pagamento_entrada', 'meio_pagamento_restante', 'meio_pagamento_avista'];
            $merged = [];
            foreach ($toNormalize as $key) {
                if ($request->has($key) && $request->input($key) !== null) {
                    $merged[$key] = $normalizePlatform($request->input($key));
                }
            }
            if (!empty($merged)) {
                $request->merge($merged);
            }

            // Normalize payment channel (Pagamento no) if provided
            $paymentChannelNames = \App\Models\PaymentChannel::pluck('name')->toArray();
            $normalizePaymentChannel = function ($value) use ($paymentChannelNames) {
                if ($value === null || $value === '') return $value;
                foreach ($paymentChannelNames as $p) {
                    if (strcasecmp($p, $value) === 0) return $p;
                }
                return $value;
            };
            $toNormalizeChannels = ['payment_location', 'payment_channel_entrada', 'payment_channel_restante', 'payment_channel_avista'];
            $mergedChannels = [];
            foreach ($toNormalizeChannels as $key) {
                if ($request->has($key) && $request->input($key) !== null) {
                    $mergedChannels[$key] = $normalizePaymentChannel($request->input($key));
                }
            }
            if (!empty($mergedChannels)) {
                $request->merge($mergedChannels);
            }

            $isParcelado = $request->boolean('parcelado');

            $rules = [
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
                "payment_location" => "nullable|string|max:255",
                "payment_means" => "nullable|string|max:255",
                // per-section payment channel fields (where payment was made)
                "payment_channel_entrada" => "nullable",
                "payment_channel_restante" => "nullable",
                "payment_channel_avista" => "nullable",
                "commercial_notes" => "nullable|string",
                "general_notes" => "nullable|string",
                "entry_channel" => "nullable|exists:entry_channels,id",
                "contrato_assinado" => "boolean",
                "contrato_na_pasta" => "boolean",
                // Novos campos obrigatórios
                "natureza_juridica" => "required|in:pessoa fisica,pessoa juridica",
                "valor_total" => "required|numeric|min:0",
                // Campos de endereço
                "cep" => "required|string|max:10",
                "endereco" => "required|string|max:255",
                "numero_casa" => "required|string|max:20",
                "complemento" => "nullable|string|max:255",
                "bairro" => "required|string|max:255",
                "cidade" => "required|string|max:255",
                "estado" => "required|string|size:2",
                // Validação de bônus
                "bonuses" => "nullable|array",
                "bonuses.*.description" => "required_with:bonuses|string|max:500",
                "bonuses.*.release_date" => "nullable|date",
                "bonuses.*.expiration_date" => "nullable|date|after_or_equal:bonuses.*.release_date",
            ];

            if ($isParcelado) {
                $rules = array_merge($rules, [
                    // aceitar id ou nome; vamos normalizar após validar
                    "meio_pagamento_entrada" => "nullable",
                    "payment_channel_entrada" => "nullable",
                    "valor_entrada" => "required|numeric|min:0",
                    "data_pagamento_entrada" => "required|date",
                    // aceitar id ou nome; vamos normalizar após validar
                    "meio_pagamento_restante" => "nullable",
                    "payment_channel_restante" => "nullable",
                    // forma pode ser 'avista'/'parcelado' ou um número/descricao vindo do payment_channel_methods (ex: '8' ou '8x')
                    "forma_pagamento_restante" => "required|string|max:255",
                    "valor_restante" => "required|numeric|min:0",
                    "data_contrato" => "required|date",
                ]);
            } else {
                $rules = array_merge($rules, [
                    "meio_pagamento_avista" => "nullable",
                    "payment_channel_avista" => "nullable",
                    "forma_pagamento_avista" => "required|string|max:255",
                    "valor_avista" => "required|numeric|min:0",
                    "data_pagamento_avista" => "required|date",
                ]);
            }

            $validated = $request->validate($rules, [
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

            // --- Normalização pós-validação: aceitar IDs (numéricos) ou nomes para plataformas e canais ---
            // Se o front enviar IDs para meio_pagamento_* converte para o nome correspondente
            $platformFields = ['meio_pagamento_entrada', 'meio_pagamento_restante', 'meio_pagamento_avista'];
            foreach ($platformFields as $f) {
                if (isset($validated[$f]) && is_numeric($validated[$f])) {
                    $pp = \App\Models\PaymentPlatform::find((int)$validated[$f]);
                    if ($pp) {
                        $validated[$f] = $pp->name; // mantém compatibilidade com o resto do código
                    }
                }
            }

            // Se o front enviar IDs para payment_channel_* converte para o name correspondente
            $channelFields = ['payment_channel_entrada', 'payment_channel_restante', 'payment_channel_avista', 'payment_location'];
            foreach ($channelFields as $f) {
                if (isset($validated[$f]) && is_numeric($validated[$f])) {
                    $pc = \App\Models\PaymentChannel::find((int)$validated[$f]);
                    if ($pc) {
                        $validated[$f] = $pc->name; // o código posterior busca por name para obter id
                    }
                }
            }
            // --- fim normalização ---

            // Buscar dados do cliente para preencher cpf_cnpj
            $client = Client::find($validated['client_id']);
            $validated['cpf_cnpj'] = $client->cpf;

            // Map inscription-level payment fields (prefer per-section first)
            if ($isParcelado) {
                $validated['payment_means'] = $validated['meio_pagamento_entrada'] ?? $validated['meio_pagamento_restante'] ?? null;
                $validated['payment_location'] = $validated['payment_channel_entrada'] ?? $validated['payment_channel_restante'] ?? null;
            } else {
                $validated['payment_means'] = $validated['meio_pagamento_avista'] ?? null;
                $validated['payment_location'] = $validated['payment_channel_avista'] ?? null;
            }

            $inscription = Inscription::create($validated);

            // Log sanitized validated data (remove sensitive fields)
            $validatedForLog = $validated;
            if (isset($validatedForLog['cpf_cnpj'])) unset($validatedForLog['cpf_cnpj']);
            Log::info('Inscription created (pre-relations)', ['id' => $inscription->id, 'validated' => $validatedForLog]);

            // Processar bônus se houver
            if ($request->has('bonuses') && is_array($request->bonuses)) {
                $bonusCount = 0;
                foreach ($request->bonuses as $bonusData) {
                    if (empty($bonusData['description'])) {
                        continue;
                    }
                    
                    $inscription->bonuses()->create([
                        'description' => trim($bonusData['description']),
                        'release_date' => $bonusData['release_date'] ?? now()->format('Y-m-d'),
                        'expiration_date' => !empty($bonusData['expiration_date']) ? $bonusData['expiration_date'] : null,
                    ]);
                    $bonusCount++;
                }
                
                if ($bonusCount > 0) {
                    Log::info("Inscription #{$inscription->id}: {$bonusCount} bônus cadastrados");
                }
            }

            // Criar endereço
            $address = $client->addresses()->create([
                'cep' => $validated['cep'],
                'endereco' => $validated['endereco'],
                'numero_casa' => $validated['numero_casa'],
                'complemento' => $validated['complemento'],
                'bairro' => $validated['bairro'],
                'cidade' => $validated['cidade'],
                'estado' => $validated['estado'],
            ]);
            Log::info('Client address created', ['address_id' => $address->id, 'client_id' => $client->id, 'inscription_id' => $inscription->id]);

            // Criar pagamentos conforme modo escolhido
            if ($isParcelado) {
                // Pagamento de Entrada
                $paymentEntry = $inscription->payments()->create([
                    'tipo' => 'Entrada',
                    'forma_pagamento' => $validated['meio_pagamento_entrada'] ?? $validated['payment_means'] ?? null,
                    'payment_channel' => $validated['payment_location'] ?? null,
                    // try to link channel id if exists
                    // prefer per-section channel id, fallback to inscription-level payment_location
                    'payment_channel_id' => isset($validated['payment_channel_entrada']) ? \App\Models\PaymentChannel::where('name', $validated['payment_channel_entrada'])->value('id') : (isset($validated['payment_location']) ? \App\Models\PaymentChannel::where('name', $validated['payment_location'])->value('id') : null),
                    'valor' => $validated['valor_entrada'],
                    'data_pagamento' => $validated['data_pagamento_entrada'],
                    'status' => 'pendente',
                ]);
                Log::info('Payment created', ['payment_id' => $paymentEntry->id, 'tipo' => 'Entrada', 'inscription_id' => $inscription->id]);

                // Pagamento Restante
                $paymentRest = $inscription->payments()->create([
                    'tipo' => 'Pagamento Restante',
                    'forma_pagamento' => $validated['meio_pagamento_restante'] ?? $validated['payment_means'] ?? null,
                    'payment_channel' => $validated['payment_location'] ?? null,
                    'payment_channel_id' => isset($validated['payment_channel_restante']) ? \App\Models\PaymentChannel::where('name', $validated['payment_channel_restante'])->value('id') : (isset($validated['payment_location']) ? \App\Models\PaymentChannel::where('name', $validated['payment_location'])->value('id') : null),
                    'valor' => $validated['valor_restante'],
                    'data_pagamento' => $validated['data_contrato'],
                    'status' => 'pendente',
                ]);
                Log::info('Payment created', ['payment_id' => $paymentRest->id, 'tipo' => 'Pagamento Restante', 'inscription_id' => $inscription->id]);
            } else {
                // Pagamento Único (à vista)
                $payment = $inscription->payments()->create([
                    'tipo' => 'Pagamento Único',
                    'forma_pagamento' => $validated['meio_pagamento_avista'] ?? $validated['payment_means'] ?? null,
                    'payment_channel' => $validated['payment_location'] ?? null,
                    'payment_channel_id' => isset($validated['payment_channel_avista']) ? \App\Models\PaymentChannel::where('name', $validated['payment_channel_avista'])->value('id') : (isset($validated['payment_location']) ? \App\Models\PaymentChannel::where('name', $validated['payment_location'])->value('id') : null),
                    'valor' => $validated['valor_avista'],
                    'data_pagamento' => $validated['data_pagamento_avista'],
                    'status' => 'pendente',
                ]);
                Log::info('Payment created', ['payment_id' => $payment->id, 'tipo' => 'Pagamento Único', 'inscription_id' => $inscription->id]);
            }

            // Recarregar a inscrição com relacionamentos necessários para o webhook
            $inscription->load(['client.addresses', 'product.webhooks', 'payments']);

            // Verificar se o webhook deve ser enviado (checkbox no formulário)
            // Aceita: 1, '1', true, 'true', 'on' como verdadeiro
            // Aceita: 0, '0', false, 'false', null como falso
            $webhookValue = $request->input('send_webhook', '1');
            $shouldSendWebhook = in_array($webhookValue, [1, '1', true, 'true', 'on'], true);

            if ($shouldSendWebhook) {
                // Disparar evento (mantém compatibilidade) e também o job do webhook com tipo explícito
                // \App\Events\InscriptionCreated::dispatch($inscription, 'inscricao.created');
                \App\Jobs\SendInscriptionWebhook::dispatch($inscription, 'inscricao.created');
                Log::info('Webhook dispatched for inscription', ['id' => $inscription->id, 'webhook_value' => $webhookValue]);
            } else {
                Log::info('Webhook skipped for inscription (user choice)', ['id' => $inscription->id, 'webhook_value' => $webhookValue]);
            }

            Log::info('Inscription processing finished', ['id' => $inscription->id]);

            return redirect()->route("inscriptions.show", $inscription)
                ->with("success", "Inscrição criada com sucesso!");
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors and rethrow to keep Laravel's default behavior
            Log::warning('Validation failed creating inscription', ['errors' => $e->errors(), 'input' => $request->all()]);
            throw $e;
        } catch (\Exception $e) {
            // Log any unexpected exception and return back with a generic message
            Log::error('Error creating inscription: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Ocorreu um erro ao criar a inscrição. Verifique os logs.');
        }
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
            "sessions.preceptorRecord", 
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

        // adicionar dados de meios/plataformas de pagamento para a view de edit
        $paymentPlatforms = \App\Models\PaymentPlatform::all();
        $paymentChannels = \App\Models\PaymentChannel::where('active', true)->orderBy('name')->get();
        
        return view("inscriptions.edit", compact("inscription", "clients", "vendors", "products", "entryChannels", "paymentPlatforms", "paymentChannels"));
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

        // Recarregar relacionamentos necessários e disparar webhook job
        $inscription->load(['client.addresses', 'product.webhooks', 'payments']);
        // \App\Events\InscriptionUpdated::dispatch($inscription, 'inscricao.updated');
        \App\Jobs\SendInscriptionWebhook::dispatch($inscription, 'inscricao.updated');

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


