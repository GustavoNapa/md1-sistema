@extends('layouts.app')

@section('title', 'Faixas de Faturamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Faixas de Faturamento</h1>
                @can('create', App\Models\FaixaFaturamento::class)
                    <a href="{{ route('faixa-faturamentos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Faixa
                    </a>
                @endcan
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('faixa-faturamentos.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar por nome</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Digite o nome da faixa">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('faixa-faturamentos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela -->
            <div class="card">
                <div class="card-body">
                    @if($faixas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Valor Mínimo</th>
                                        <th>Valor Máximo</th>
                                        <th>Range</th>
                                        <th>Criado em</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faixas as $faixa)
                                        <tr>
                                            <td>
                                                <strong>{{ $faixa->label }}</strong>
                                            </td>
                                            <td>{{ $faixa->valor_min_formatted }}</td>
                                            <td>{{ $faixa->valor_max_formatted }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $faixa->range_formatted }}</span>
                                            </td>
                                            <td>{{ $faixa->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('faixa-faturamentos.show', $faixa) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('update', $faixa)
                                                        <a href="{{ route('faixa-faturamentos.edit', $faixa) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $faixa)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                title="Excluir" data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal{{ $faixa->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>

                                        @can('delete', $faixa)
                                            <!-- Modal de Confirmação -->
                                            <div class="modal fade" id="deleteModal{{ $faixa->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmar Exclusão</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja excluir a faixa <strong>{{ $faixa->label }}</strong>?
                                                            <br><small class="text-muted">Esta ação não pode ser desfeita.</small>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form method="POST" action="{{ route('faixa-faturamentos.destroy', $faixa) }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $faixas->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma faixa de faturamento encontrada</h5>
                            <p class="text-muted">
                                @if(request('search'))
                                    Tente ajustar os filtros de busca.
                                @else
                                    Comece criando sua primeira faixa de faturamento.
                                @endif
                            </p>
                            @can('create', App\Models\FaixaFaturamento::class)
                                <a href="{{ route('faixa-faturamentos.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nova Faixa
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

