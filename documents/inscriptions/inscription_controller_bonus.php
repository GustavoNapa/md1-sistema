<?php

/**
 * ============================================================
 * CÓDIGO PARA ADICIONAR NO InscriptionController.php
 * ============================================================
 * 
 * Este código deve ser integrado ao método store() existente.
 * Adicione a lógica de processamento de bônus APÓS a criação
 * da inscrição e ANTES do webhook (se houver).
 */

// ============================================================
// 1. ADICIONAR NA VALIDAÇÃO DO MÉTODO store()
// ============================================================
// Adicione estas regras no array de validação:

$rules = [
    // ... outras regras existentes ...
    
    // Validação para bônus
    'bonuses' => 'nullable|array',
    'bonuses.*.description' => 'required_with:bonuses|string|max:500',
    'bonuses.*.release_date' => 'nullable|date',
    'bonuses.*.expiration_date' => 'nullable|date|after_or_equal:bonuses.*.release_date',
];

$messages = [
    // ... outras mensagens existentes ...
    
    // Mensagens para bônus
    'bonuses.*.description.required_with' => 'A descrição do bônus é obrigatória.',
    'bonuses.*.description.max' => 'A descrição do bônus não pode ter mais de 500 caracteres.',
    'bonuses.*.expiration_date.after_or_equal' => 'A data de expiração deve ser igual ou posterior à data de liberação.',
];


// ============================================================
// 2. CÓDIGO PARA PROCESSAR OS BÔNUS APÓS CRIAR A INSCRIÇÃO
// ============================================================
// Adicione este código APÓS a linha: $inscription = Inscription::create($data);

// Processar bônus se houver
if ($request->has('bonuses') && is_array($request->bonuses)) {
    foreach ($request->bonuses as $bonusData) {
        // Pular se a descrição estiver vazia
        if (empty($bonusData['description'])) {
            continue;
        }
        
        $inscription->bonuses()->create([
            'description' => $bonusData['description'],
            'release_date' => $bonusData['release_date'] ?? now()->format('Y-m-d'),
            'expiration_date' => $bonusData['expiration_date'] ?? null,
        ]);
    }
}


// ============================================================
// 3. MÉTODO STORE COMPLETO (EXEMPLO DE INTEGRAÇÃO)
// ============================================================
// Este é um exemplo de como o método store ficaria com a integração:

public function store(Request $request)
{
    $validated = $request->validate([
        // Validações existentes...
        'client_id' => 'required|exists:clients,id',
        'vendor_id' => 'nullable|exists:vendors,id',
        'product_id' => 'required|exists:products,id',
        'status' => 'required|string',
        'valor_total' => 'required|numeric|min:0',
        // ... outras validações ...
        
        // Validação de bônus
        'bonuses' => 'nullable|array',
        'bonuses.*.description' => 'required_with:bonuses|string|max:500',
        'bonuses.*.release_date' => 'nullable|date',
        'bonuses.*.expiration_date' => 'nullable|date|after_or_equal:bonuses.*.release_date',
    ]);
    
    // Criar a inscrição
    $inscription = Inscription::create([
        // ... campos da inscrição ...
    ]);
    
    // ========== PROCESSAR BÔNUS ==========
    if ($request->has('bonuses') && is_array($request->bonuses)) {
        $bonusCount = 0;
        foreach ($request->bonuses as $bonusData) {
            if (empty($bonusData['description'])) {
                continue;
            }
            
            $inscription->bonuses()->create([
                'description' => trim($bonusData['description']),
                'release_date' => $bonusData['release_date'] ?? now()->format('Y-m-d'),
                'expiration_date' => !empty($bonusData['expiration_date']) 
                    ? $bonusData['expiration_date'] 
                    : null,
            ]);
            $bonusCount++;
        }
        
        // Log opcional
        if ($bonusCount > 0) {
            \Log::info("Inscrição #{$inscription->id}: {$bonusCount} bônus cadastrados");
        }
    }
    // =====================================
    
    // ... resto do código (webhook, redirect, etc.) ...
    
    return redirect()
        ->route('inscriptions.show', $inscription)
        ->with('success', 'Inscrição criada com sucesso!');
}


// ============================================================
// 4. INCLUIR BÔNUS NO WEBHOOK (se necessário)
// ============================================================
// Se você envia dados via webhook, adicione os bônus ao payload:

$webhookPayload = [
    // ... outros dados ...
    
    'bonuses' => $inscription->bonuses->map(function($bonus) {
        return [
            'description' => $bonus->description,
            'release_date' => $bonus->release_date ? $bonus->release_date->format('Y-m-d') : null,
            'expiration_date' => $bonus->expiration_date ? $bonus->expiration_date->format('Y-m-d') : null,
        ];
    })->toArray(),
];
