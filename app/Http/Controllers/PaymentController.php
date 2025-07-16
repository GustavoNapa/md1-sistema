<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'valor' => 'required|numeric|min:0',
            'data_pagamento' => 'nullable|date',
            'forma_pagamento' => 'nullable|string|max:50',
            'status' => 'required|in:pendente,pago,cancelado',
            'contrato_assinado' => 'boolean',
            'contrato_na_pasta' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento registrado com sucesso!',
            'data' => $payment
        ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pagamento excluído com sucesso!'
        ]);
    }
}
