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

                        <!-- Seção de Webhook -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Configuração de Webhook</h5>
                            
                            <div class="mb-3">
                                <label for="webhook_url" class="form-label">URL do Webhook</label>
                                <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                                       id="webhook_url" name="webhook_url" value="{{ old('webhook_url') }}" 
                                       placeholder="https://exemplo.com/webhook">
                                @error('webhook_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">URL que receberá os dados das inscrições deste produto</div>
                            </div>

                            <div class="mb-3">
                                <label for="webhook_token" class="form-label">Token de Autorização</label>
                                <input type="text" class="form-control @error('webhook_token') is-invalid @enderror" 
                                       id="webhook_token" name="webhook_token" value="{{ old('webhook_token') }}" 
                                       placeholder="Bearer token ou chave de API">
                                @error('webhook_token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Token que será enviado no cabeçalho Authorization</div>
                            </div>

                            <div class="mb-3">
                                <label for="webhook_trigger_status" class="form-label">Status Gatilho</label>
                                <select class="form-select @error('webhook_trigger_status') is-invalid @enderror" 
                                        id="webhook_trigger_status" name="webhook_trigger_status">
                                    <option value="active" {{ old('webhook_trigger_status', 'active') == 'active' ? 'selected' : '' }}>Ativo</option>
                                    <option value="paused" {{ old('webhook_trigger_status') == 'paused' ? 'selected' : '' }}>Pausado</option>
                                    <option value="cancelled" {{ old('webhook_trigger_status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                    <option value="completed" {{ old('webhook_trigger_status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                                </select>
                                @error('webhook_trigger_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Webhook será disparado apenas quando a inscrição tiver este status</div>
                            </div>
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
