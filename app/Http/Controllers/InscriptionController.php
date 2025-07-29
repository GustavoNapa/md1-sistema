<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Product;
use App\Events\InscriptionCreated;
use App\Events\InscriptionUpdated;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inscriptions = Inscription::with(["client", "vendor", "product"])
            ->orderBy("created_at", "desc")
            ->paginate(15);

        return view("inscriptions.index", compact("inscriptions"));
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
            \Log::error("Erro na API do Kanban: " . $e->getMessage() . "\n" . $e->getTraceAsString());
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
        
        return view("inscriptions.create", compact("clients", "vendors", "products", "entryChannels"));
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

        $inscription = Inscription::create($validated);

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


