# Guia de Integração: Seção de Bônus no Cadastro de Inscrições

## Visão Geral

Esta integração adiciona uma seção amigável para cadastrar bônus prometidos durante a venda, 
diretamente no formulário de criação de inscrições.

## Arquivos a Modificar

1. `resources/views/inscriptions/create.blade.php` - Adicionar seção de bônus
2. `app/Http/Controllers/InscriptionController.php` - Processar bônus no método store()
3. `app/Models/Bonus.php` - Verificar/ajustar relacionamento (se necessário)

---

## 1. Modificar create.blade.php

### Passo 1.1: Localizar onde inserir

Abra o arquivo `create.blade.php` e localize a seção de **Observações** (por volta da linha 491-511):

```blade
<!-- Observações -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="commercial_notes" class="form-label">Observações Comerciais</label>
            ...
        </div>
    </div>
    ...
</div>
```

### Passo 1.2: Inserir a seção de bônus

Cole o código abaixo **APÓS** a seção de Observações e **ANTES** do botão de submit:

```blade
<!-- ========== INÍCIO SEÇÃO DE BÔNUS ========== -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-gift text-primary me-2"></i>
            Bônus Concedidos na Venda
        </h5>
        <button type="button" class="btn btn-primary btn-sm" id="addBonus">
            <i class="fas fa-plus me-1"></i> Adicionar Bônus
        </button>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            <i class="fas fa-info-circle me-1"></i>
            Cadastre aqui os bônus prometidos ao cliente durante a venda.
        </p>
        
        <div id="bonuses-container"></div>
        
        <div id="no-bonuses-message" class="text-center py-4">
            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Nenhum bônus adicionado ainda.</p>
            <small class="text-muted">Clique em "Adicionar Bônus" para incluir bônus prometidos na venda.</small>
        </div>
    </div>
</div>

<template id="bonus-item-template">
    <div class="bonus-item card border-start border-primary border-3 mb-3" data-bonus-index="__INDEX__">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <span class="badge bg-primary me-2">Bônus #<span class="bonus-number">__NUMBER__</span></span>
                </h6>
                <button type="button" class="btn btn-outline-danger btn-sm remove-bonus" title="Remover este bônus">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Descrição do Bônus *</label>
                    <input type="text" 
                           class="form-control" 
                           name="bonuses[__INDEX__][description]" 
                           placeholder="Ex: 6 meses de acesso à Universidade Secretária Médica"
                           required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data de Liberação</label>
                    <input type="date" 
                           class="form-control" 
                           name="bonuses[__INDEX__][release_date]">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data de Expiração <small class="text-muted">(Opcional)</small></label>
                    <input type="date" 
                           class="form-control" 
                           name="bonuses[__INDEX__][expiration_date]">
                </div>
            </div>
        </div>
    </div>
</template>
<!-- ========== FIM SEÇÃO DE BÔNUS ========== -->
```

### Passo 1.3: Adicionar o JavaScript

No final do arquivo, dentro da seção `@section('scripts')` ou antes do `@endsection`, 
adicione o seguinte JavaScript:

```javascript
// ========== GERENCIAMENTO DE BÔNUS ==========
(function() {
    let bonusIndex = 0;
    const container = document.getElementById('bonuses-container');
    const template = document.getElementById('bonus-item-template');
    const addBtn = document.getElementById('addBonus');
    const noMessage = document.getElementById('no-bonuses-message');
    
    if (!container || !template || !addBtn) return;
    
    function addBonus(description = '', releaseDate = '', expirationDate = '') {
        const html = template.innerHTML
            .replace(/__INDEX__/g, bonusIndex)
            .replace(/__NUMBER__/g, bonusIndex + 1);
        
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const bonusElement = wrapper.firstElementChild;
        
        if (description) {
            bonusElement.querySelector('input[name*="[description]"]').value = description;
        }
        if (releaseDate) {
            bonusElement.querySelector('input[name*="[release_date]"]').value = releaseDate;
        }
        if (expirationDate) {
            bonusElement.querySelector('input[name*="[expiration_date]"]').value = expirationDate;
        }
        
        container.appendChild(bonusElement);
        bonusIndex++;
        updateUI();
        
        if (!description) {
            setTimeout(() => {
                bonusElement.querySelector('input[name*="[description]"]').focus();
            }, 100);
        }
    }
    
    function removeBonus(element) {
        element.closest('.bonus-item').remove();
        renumberBonuses();
        updateUI();
    }
    
    function renumberBonuses() {
        const items = container.querySelectorAll('.bonus-item');
        items.forEach((item, idx) => {
            item.querySelector('.bonus-number').textContent = idx + 1;
        });
    }
    
    function updateUI() {
        const hasItems = container.children.length > 0;
        noMessage.style.display = hasItems ? 'none' : 'block';
    }
    
    addBtn.addEventListener('click', function() {
        addBonus();
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-bonus')) {
            e.preventDefault();
            if (confirm('Remover este bônus?')) {
                removeBonus(e.target);
            }
        }
    });
    
    updateUI();
    
    // Restaurar bônus do old() em caso de erro de validação
    @if(old('bonuses'))
        @foreach(old('bonuses') as $idx => $bonus)
            addBonus(
                @json($bonus['description'] ?? ''),
                @json($bonus['release_date'] ?? ''),
                @json($bonus['expiration_date'] ?? '')
            );
        @endforeach
    @endif
})();
// ========== FIM GERENCIAMENTO DE BÔNUS ==========
```

### Passo 1.4: Adicionar CSS (opcional)

Na seção `@section('styles')`, adicione:

```css
.bonus-item {
    transition: all 0.3s ease;
}
.bonus-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.bonus-item .remove-bonus {
    opacity: 0.5;
    transition: opacity 0.2s;
}
.bonus-item:hover .remove-bonus {
    opacity: 1;
}
```

---

## 2. Modificar InscriptionController.php

### Passo 2.1: Adicionar validação no método store()

Localize a validação existente e adicione as regras para bônus:

```php
$validated = $request->validate([
    // ... regras existentes ...
    
    // Adicionar estas regras:
    'bonuses' => 'nullable|array',
    'bonuses.*.description' => 'required_with:bonuses|string|max:500',
    'bonuses.*.release_date' => 'nullable|date',
    'bonuses.*.expiration_date' => 'nullable|date|after_or_equal:bonuses.*.release_date',
]);
```

### Passo 2.2: Processar bônus após criar a inscrição

Após a linha que cria a inscrição (`$inscription = Inscription::create(...)`), adicione:

```php
// Processar bônus
if ($request->has('bonuses') && is_array($request->bonuses)) {
    foreach ($request->bonuses as $bonusData) {
        if (empty($bonusData['description'])) {
            continue;
        }
        
        $inscription->bonuses()->create([
            'description' => trim($bonusData['description']),
            'release_date' => $bonusData['release_date'] ?? now()->format('Y-m-d'),
            'expiration_date' => $bonusData['expiration_date'] ?? null,
        ]);
    }
}
```

### Passo 2.3: Incluir bônus no webhook (se aplicável)

Se você envia dados para webhook, adicione os bônus ao payload:

```php
$webhookData = [
    // ... outros dados ...
    'bonuses' => $inscription->bonuses->map(fn($b) => [
        'description' => $b->description,
        'release_date' => $b->release_date?->format('Y-m-d'),
        'expiration_date' => $b->expiration_date?->format('Y-m-d'),
    ])->toArray(),
];
```

---

## 3. Verificar Modelo Bonus.php

O modelo Bonus.php deve ter a relação correta:

```php
public function inscription()
{
    return $this->belongsTo(Inscription::class, 'subscription_id');
}
```

E o modelo Inscription.php deve ter:

```php
public function bonuses()
{
    return $this->hasMany(\App\Models\Bonus::class, 'subscription_id', 'id');
}
```

---

## 4. Testar a Integração

1. Acesse a tela de criação de inscrição
2. Clique em "Adicionar Bônus"
3. Preencha a descrição e datas
4. Adicione mais bônus se necessário
5. Salve a inscrição
6. Verifique na aba "Bônus" da inscrição criada

---

## Resultado Final

Após a integração, os vendedores poderão:
- Adicionar múltiplos bônus durante o cadastro da inscrição
- Definir data de liberação e expiração para cada bônus
- Visualizar os bônus cadastrados na aba "Bônus" da inscrição

Isso elimina a necessidade de colocar informações de bônus nas observações.
