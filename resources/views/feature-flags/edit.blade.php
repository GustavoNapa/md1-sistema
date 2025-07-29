@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Funcionalidade: {{ $feature['name'] }}
                </h1>
                <div>
                    <a href="{{ route('feature-flags.show', $feature['key']) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Configurações da Funcionalidade
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('feature-flags.update', $feature['key']) }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome da Funcionalidade</label>
                                        <input type="text" class="form-control" value="{{ $feature['name'] }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Chave</label>
                                        <input type="text" class="form-control" value="{{ $feature['key'] }}" readonly>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Categoria</label>
                                        <input type="text" class="form-control" value="{{ $feature['category'] }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Atual</label>
                                        <div class="form-control d-flex align-items-center">
                                            <span class="badge {{ $feature['enabled'] ? 'bg-success' : 'bg-secondary' }} me-2">
                                                <i class="fas {{ $feature['enabled'] ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
                                                {{ $feature['enabled'] ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $feature['description'] }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Configuração de Status</label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" {{ $feature['enabled'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enabled">
                                                    <strong>Funcionalidade Ativa</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Marque esta opção para ativar a funcionalidade globalmente no sistema.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('feature-flags.show', $feature['key']) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Informações de Ajuda -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>Status da Funcionalidade</h6>
                            <p class="small text-muted">
                                O status determina se a funcionalidade está disponível para uso no sistema. 
                                Quando ativa, a funcionalidade será executada normalmente. Quando inativa, 
                                será ignorada pelo sistema.
                            </p>

                            <h6 class="mt-3">Impacto das Alterações</h6>
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                As alterações têm efeito imediato no sistema. Certifique-se de testar 
                                em ambiente de desenvolvimento antes de aplicar em produção.
                            </div>
                        </div>
                    </div>

                    <!-- Configurações Avançadas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Configurações Avançadas
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>Escopo por Usuário</h6>
                            <p class="small text-muted">
                                Para configurações mais avançadas, como ativar a funcionalidade apenas 
                                para usuários específicos, use o Laravel Pennant diretamente no código 
                                ou através de comandos Artisan.
                            </p>

                            <div class="mt-3">
                                <h6>Comandos Úteis:</h6>
                                <pre class="bg-light p-2 rounded small"><code># Ativar para usuário específico
php artisan pennant:feature {{ $feature['key'] }} --user=1

# Desativar globalmente
php artisan pennant:feature {{ $feature['key'] }} --deactivate</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-check-input {
    transform: scale(1.2);
}

pre {
    font-size: 0.75rem;
    line-height: 1.4;
}

code {
    color: #495057;
}

.card-title {
    color: #495057;
    font-weight: 600;
}
</style>
@endpush

