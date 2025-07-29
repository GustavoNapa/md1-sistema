<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Models\Inscription;
use App\Models\ProductWebhook;
use App\Jobs\ProcessInscriptionWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookLogController extends Controller
{
    /**
     * Display a listing of the webhook logs.
     */
    public function index(Request $request)
    {
        $query = WebhookLog::with(['inscription.client', 'inscription.product'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('inscription_id')) {
            $query->where('inscription_id', $request->inscription_id);
        }

        if ($request->filled('webhook_url')) {
            $query->where('webhook_url', 'like', '%' . $request->webhook_url . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $webhookLogs = $query->paginate(20);

        return view('webhook-logs.index', compact('webhookLogs'));
    }

    /**
     * Display the specified webhook log.
     */
    public function show(WebhookLog $webhookLog)
    {
        $webhookLog->load(['inscription.client', 'inscription.product']);
        
        return view('webhook-logs.show', compact('webhookLog'));
    }

    /**
     * Resend a webhook.
     */
    public function resend(WebhookLog $webhookLog)
    {
        try {
            // Buscar o webhook do produto associado à inscrição
            $productWebhook = ProductWebhook::where('product_id', $webhookLog->inscription->product_id)
                ->where('webhook_trigger_status', $webhookLog->inscription->status)
                ->first();

            if (!$productWebhook) {
                return redirect()->back()->with('error', 'Webhook do produto não encontrado ou status não corresponde.');
            }

            // Disparar o job novamente
            ProcessInscriptionWebhook::dispatch($webhookLog->inscription, $productWebhook);

            Log::info('Webhook reenviado manualmente', [
                'webhook_log_id' => $webhookLog->id,
                'inscription_id' => $webhookLog->inscription_id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Webhook reenviado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao reenviar webhook', [
                'webhook_log_id' => $webhookLog->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'Erro ao reenviar webhook: ' . $e->getMessage());
        }
    }

    /**
     * Get status options for filters
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pendente',
            'success' => 'Sucesso',
            'failed' => 'Falha',
        ];
    }

    /**
     * Get status badge class for display
     */
    public static function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-warning',
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status label for display
     */
    public static function getStatusLabel($status)
    {
        return self::getStatusOptions()[$status] ?? $status;
    }
}

