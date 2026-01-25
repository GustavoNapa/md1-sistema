{{-- 
    Seção de Bônus para o formulário de criação de inscrição
    Inserir este código após a seção de Observações (linha ~511) e antes do botão de submit
--}}

<!-- Seção Bônus -->
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
            Cadastre aqui os bônus prometidos ao cliente durante a venda. Você pode adicionar quantos bônus forem necessários.
        </p>
        
        <!-- Container para os bônus -->
        <div id="bonuses-container">
            <!-- Bônus serão adicionados dinamicamente aqui -->
        </div>
        
        <!-- Estado vazio -->
        <div id="no-bonuses-message" class="text-center py-4">
            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Nenhum bônus adicionado ainda.</p>
            <small class="text-muted">Clique em "Adicionar Bônus" para incluir bônus prometidos na venda.</small>
        </div>
    </div>
</div>

{{-- Template do item de bônus (será clonado via JavaScript) --}}
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
                    <div class="form-text">Descreva o bônus prometido ao cliente</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data de Liberação</label>
                    <input type="date" 
                           class="form-control" 
                           name="bonuses[__INDEX__][release_date]">
                    <div class="form-text">Quando o bônus será liberado para o cliente</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data de Expiração <small class="text-muted">(Opcional)</small></label>
                    <input type="date" 
                           class="form-control" 
                           name="bonuses[__INDEX__][expiration_date]">
                    <div class="form-text">Quando o bônus expira (deixe vazio se não expira)</div>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- Bônus comuns sugeridos (para facilitar o preenchimento) --}}
<div class="modal fade" id="modalBonusSuggestions" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb me-2"></i>
                    Bônus Frequentes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Selecione os bônus que deseja adicionar:</p>
                <div class="row" id="suggested-bonuses">
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Encontro CRM Black Mind" id="bonus1">
                            <label class="form-check-label" for="bonus1">Encontro CRM Black Mind</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Implementação de Funil de Leads" id="bonus2">
                            <label class="form-check-label" for="bonus2">Implementação de Funil de Leads</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="6 meses de acesso à Universidade Secretária Médica" id="bonus3">
                            <label class="form-check-label" for="bonus3">6 meses Universidade Secretária Médica</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="3 meses de degustação do Meuk (CRM)" id="bonus4">
                            <label class="form-check-label" for="bonus4">3 meses degustação Meuk (CRM)</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Vitrine Médico Celebridade (todos os cursos)" id="bonus5">
                            <label class="form-check-label" for="bonus5">Vitrine Médico Celebridade (todos os cursos)</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Academia Médico Mentor" id="bonus6">
                            <label class="form-check-label" for="bonus6">Academia Médico Mentor</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Consultoria de Instagram" id="bonus7">
                            <label class="form-check-label" for="bonus7">Consultoria de Instagram</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input bonus-suggestion" type="checkbox" value="Setup Completo de Automações" id="bonus8">
                            <label class="form-check-label" for="bonus8">Setup Completo de Automações</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="addSelectedBonuses">
                    <i class="fas fa-plus me-1"></i> Adicionar Selecionados
                </button>
            </div>
        </div>
    </div>
</div>

{{-- CSS específico para a seção de bônus --}}
<style>
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

#bonuses-container:empty + #no-bonuses-message {
    display: block !important;
}

#bonuses-container:not(:empty) + #no-bonuses-message {
    display: none !important;
}

.bonus-suggestion:checked + label {
    font-weight: 600;
    color: #0d6efd;
}
</style>

{{-- JavaScript para gerenciar os bônus --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let bonusIndex = 0;
    const container = document.getElementById('bonuses-container');
    const template = document.getElementById('bonus-item-template');
    const addBtn = document.getElementById('addBonus');
    const noMessage = document.getElementById('no-bonuses-message');
    
    // Função para adicionar um bônus
    function addBonus(description = '', releaseDate = '', expirationDate = '') {
        const html = template.innerHTML
            .replace(/__INDEX__/g, bonusIndex)
            .replace(/__NUMBER__/g, bonusIndex + 1);
        
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const bonusElement = wrapper.firstElementChild;
        
        // Preencher valores se fornecidos
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
        
        // Focar no campo de descrição
        setTimeout(() => {
            const descInput = bonusElement.querySelector('input[name*="[description]"]');
            if (descInput && !description) {
                descInput.focus();
            }
        }, 100);
    }
    
    // Função para remover um bônus
    function removeBonus(element) {
        element.closest('.bonus-item').remove();
        renumberBonuses();
        updateUI();
    }
    
    // Renumerar os bônus após remoção
    function renumberBonuses() {
        const items = container.querySelectorAll('.bonus-item');
        items.forEach((item, idx) => {
            item.querySelector('.bonus-number').textContent = idx + 1;
        });
    }
    
    // Atualizar UI (mostrar/ocultar mensagem de vazio)
    function updateUI() {
        const hasItems = container.children.length > 0;
        noMessage.style.display = hasItems ? 'none' : 'block';
    }
    
    // Event listener para adicionar bônus
    addBtn.addEventListener('click', function() {
        addBonus();
    });
    
    // Event delegation para remover bônus
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-bonus')) {
            e.preventDefault();
            const item = e.target.closest('.bonus-item');
            
            // Confirmação
            if (confirm('Tem certeza que deseja remover este bônus?')) {
                removeBonus(e.target);
            }
        }
    });
    
    // Adicionar bônus selecionados do modal de sugestões
    const addSelectedBtn = document.getElementById('addSelectedBonuses');
    if (addSelectedBtn) {
        addSelectedBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.bonus-suggestion:checked');
            const today = new Date().toISOString().split('T')[0];
            
            checkboxes.forEach(cb => {
                addBonus(cb.value, today);
                cb.checked = false;
            });
            
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalBonusSuggestions'));
            if (modal) modal.hide();
        });
    }
    
    // Inicializar UI
    updateUI();
    
    // Restaurar bônus do old() se houver erro de validação
    @if(old('bonuses'))
        @foreach(old('bonuses') as $idx => $bonus)
            addBonus(
                '{{ $bonus['description'] ?? '' }}',
                '{{ $bonus['release_date'] ?? '' }}',
                '{{ $bonus['expiration_date'] ?? '' }}'
            );
        @endforeach
    @endif
});
</script>
