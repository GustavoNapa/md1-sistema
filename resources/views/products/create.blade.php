@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Novo Produto</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Produto *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Preço *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" 
                                               step="0.01" min="0" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="offer_price" class="form-label">Preço de Oferta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('offer_price') is-invalid @enderror" 
                                               id="offer_price" name="offer_price" value="{{ old('offer_price') }}" 
                                               step="0.01" min="0">
                                        @error('offer_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Deixe em branco se não houver oferta</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                       type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Produto Ativo
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Seção de Webhooks -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Configuração de Webhooks</h5>
                            
                            <div id="webhooks-container">
                                <div class="webhook-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="webhook_url_0" class="form-label">URL do Webhook</label>
                                            <input type="url" class="form-control" 
                                                   id="webhook_url_0" name="webhooks[0][webhook_url]" 
                                                   placeholder="https://exemplo.com/webhook">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="webhook_trigger_status_0" class="form-label">Status Gatilho</label>
                                            <select class="form-select" 
                                                    id="webhook_trigger_status_0" name="webhooks[0][webhook_trigger_status]">
                                                <option value="active">Ativo</option>
                                                <option value="paused">Pausado</option>
                                                <option value="cancelled">Cancelado</option>
                                                <option value="completed">Concluído</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-webhook" disabled>
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <label for="webhook_token_0" class="form-label">Token de Autorização</label>
                                            <input type="text" class="form-control" 
                                                   id="webhook_token_0" name="webhooks[0][webhook_token]" 
                                                   placeholder="Bearer token ou chave de API">
                                            <div class="form-text">Token que será enviado no cabeçalho Authorization</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-webhook">
                                <i class="fas fa-plus"></i> Adicionar Webhook
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let webhookIndex = 1;
    
    function updateRemoveButtons() {
        const webhookItems = document.querySelectorAll('.webhook-item');
        const removeButtons = document.querySelectorAll('.remove-webhook');
        
        removeButtons.forEach(button => {
            button.disabled = webhookItems.length <= 1;
        });
    }
    
    document.getElementById('add-webhook').addEventListener('click', function() {
        const container = document.getElementById('webhooks-container');
        const newWebhook = document.createElement('div');
        newWebhook.className = 'webhook-item border rounded p-3 mb-3';
        newWebhook.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label for="webhook_url_${webhookIndex}" class="form-label">URL do Webhook</label>
                    <input type="url" class="form-control" 
                           id="webhook_url_${webhookIndex}" name="webhooks[${webhookIndex}][webhook_url]" 
                           placeholder="https://exemplo.com/webhook">
                </div>
                <div class="col-md-3">
                    <label for="webhook_trigger_status_${webhookIndex}" class="form-label">Status Gatilho</label>
                    <select class="form-select" 
                            id="webhook_trigger_status_${webhookIndex}" name="webhooks[${webhookIndex}][webhook_trigger_status]">
                        <option value="active">Ativo</option>
                        <option value="paused">Pausado</option>
                        <option value="cancelled">Cancelado</option>
                        <option value="completed">Concluído</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-webhook">
                            <i class="fas fa-trash"></i> Remover
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label for="webhook_token_${webhookIndex}" class="form-label">Token de Autorização</label>
                    <input type="text" class="form-control" 
                           id="webhook_token_${webhookIndex}" name="webhooks[${webhookIndex}][webhook_token]" 
                           placeholder="Bearer token ou chave de API">
                    <div class="form-text">Token que será enviado no cabeçalho Authorization</div>
                </div>
            </div>
        `;
        
        container.appendChild(newWebhook);
        webhookIndex++;
        updateRemoveButtons();
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-webhook') || e.target.closest('.remove-webhook')) {
            const webhookItem = e.target.closest('.webhook-item');
            webhookItem.remove();
            updateRemoveButtons();
        }
    });
    
    updateRemoveButtons();
});
</script>
@endsection

