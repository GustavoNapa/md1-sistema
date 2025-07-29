@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-toggle-on me-2"></i>{{ $feature['name'] }}
                </h1>
                <div>
                    <a href="{{ route('feature-flags.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="{{ route('feature-flags.edit', $feature['key']) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações Gerais -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações Gerais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Nome:</strong></div>
                                <div class="col-sm-9">{{ $feature['name'] }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Chave:</strong></div>
                                <div class="col-sm-9">
                                    <code>{{ $feature['key'] }}</code>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Categoria:</strong></div>
                                <div class="col-sm-9">
                                    <span class="badge bg-info">{{ $feature['category'] }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Status:</strong></div>
                                <div class="col-sm-9">
                                    <span class="badge {{ $feature['enabled'] ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas {{ $feature['enabled'] ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
                                        {{ $feature['enabled'] ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3"><strong>Descrição:</strong></div>
                                <div class="col-sm-9">{{ $feature['description'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Escopos Ativos -->
                    @if($activeScopes->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users me-2"></i>Escopos Ativos
                                    <span class="badge bg-primary ms-2">{{ $activeScopes->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo de Escopo</th>
                                                <th>ID do Escopo</th>
                                                <th>Valor</th>
                                                <th>Criado em</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeScopes as $scope)
                                                <tr>
                                                    <td>
                                                        @if($scope->scope_type)
                                                            <span class="badge bg-secondary">{{ $scope->scope_type }}</span>
                                                        @else
                                                            <span class="badge bg-success">Global</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($scope->scope_id)
                                                            <code>{{ $scope->scope_id }}</code>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ $scope->value }}</span>
                                                    </td>
                                                    <td>
                                                        @if($scope->created_at)
                                                            {{ \Carbon\Carbon::parse($scope->created_at)->format('d/m/Y H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users me-2"></i>Escopos Ativos
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Nenhum escopo ativo</h6>
                                    <p class="text-muted small">Esta funcionalidade não está ativa para nenhum escopo específico.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Painel de Controle -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Controle Rápido
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form method="POST" action="{{ route('feature-flags.update', $feature['key']) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="enabled" value="{{ $feature['enabled'] ? '0' : '1' }}">
                                    <button type="submit" class="btn {{ $feature['enabled'] ? 'btn-warning' : 'btn-success' }} w-100">
                                        <i class="fas {{ $feature['enabled'] ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i>
                                        {{ $feature['enabled'] ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                                
                                <a href="{{ route('feature-flags.edit', $feature['key']) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-2"></i>Configurar
                                </a>
                                
                                <form method="POST" action="{{ route('feature-flags.destroy', $feature['key']) }}" 
                                      onsubmit="return confirm('Tem certeza que deseja remover todos os registros desta funcionalidade?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Limpar Registros
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Técnicas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-code me-2"></i>Informações Técnicas
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>Como usar no código:</h6>
                            <div class="mb-3">
                                <label class="form-label small">PHP (Laravel):</label>
                                <pre class="bg-light p-2 rounded small"><code>use Laravel\Pennant\Feature;

if (Feature::active('{{ $feature['key'] }}')) {
    // Funcionalidade ativa
}</code></pre>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small">Blade Template:</label>
                                <pre class="bg-light p-2 rounded small"><code>@feature('{{ $feature['key'] }}')
    &lt;!-- Conteúdo da funcionalidade --&gt;
@endfeature</code></pre>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small">Middleware:</label>
                                <pre class="bg-light p-2 rounded small"><code>Route::middleware('feature:{{ $feature['key'] }}')
    ->group(function () {
        // Rotas protegidas
    });</code></pre>
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
pre {
    font-size: 0.8rem;
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

